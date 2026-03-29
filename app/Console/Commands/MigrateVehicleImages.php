<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateVehicleImages extends Command
{
    protected $signature = 'vehicles:migrate-images';

    protected $description = 'Traiter toutes les photos principales de véhicules existants via l\'API showroom';

    private const API_URL = 'https://api.resadz.com/process-image';
    private const API_TOKEN = 'resadz_vps_secret_2026';

    public function handle(): int
    {
        $disk = Storage::disk('public');

        // Créer le dossier originals si nécessaire
        if (!$disk->exists('vehicles/originals')) {
            $disk->makeDirectory('vehicles/originals');
        }

        $vehicles = Vehicle::whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        if ($vehicles->isEmpty()) {
            $this->info('Aucun véhicule avec photo principale trouvé.');
            return self::SUCCESS;
        }

        $this->info("Traitement de {$vehicles->count()} véhicule(s)...");
        $this->newLine();

        $processed = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($vehicles->count());
        $bar->start();

        foreach ($vehicles as $vehicle) {
            $currentImagePath = $vehicle->image;
            $filename = pathinfo($currentImagePath, PATHINFO_FILENAME);
            $extension = pathinfo($currentImagePath, PATHINFO_EXTENSION);
            $originalPath = 'vehicles/originals/' . $filename . '.' . $extension;

            // Déterminer quelle image envoyer à l'API
            if ($disk->exists($originalPath)) {
                // L'original existe → l'utiliser (on re-traite depuis la source)
                $sourceImagePath = $originalPath;
            } elseif ($disk->exists($currentImagePath)) {
                // Pas d'original → sauvegarder l'image actuelle comme original
                $disk->copy($currentImagePath, $originalPath);
                $sourceImagePath = $originalPath;
            } else {
                $this->newLine();
                $this->warn("  Skip #{$vehicle->id} ({$vehicle->full_name}) — fichier introuvable");
                $errors++;
                $bar->advance();
                continue;
            }

            // Envoyer l'original à l'API
            try {
                $fullPath = $disk->path($sourceImagePath);

                $response = Http::timeout(30)
                    ->withHeaders(['X-API-Token' => self::API_TOKEN])
                    ->attach('image', file_get_contents($fullPath), basename($fullPath))
                    ->post(self::API_URL);

                if (!$response->successful()) {
                    $this->newLine();
                    $this->warn("  Erreur API #{$vehicle->id} — HTTP {$response->status()}");
                    $errors++;
                    $bar->advance();
                    continue;
                }

                $processedContent = $response->body();

                if (empty($processedContent) || strlen($processedContent) < 1000) {
                    $this->newLine();
                    $this->warn("  Erreur API #{$vehicle->id} — réponse trop petite");
                    $errors++;
                    $bar->advance();
                    continue;
                }

                // Sauvegarder l'image traitée à la place de l'image actuelle
                $processedPath = 'vehicles/' . $filename . '.jpg';
                $disk->put($processedPath, $processedContent);

                // Supprimer l'ancien fichier si le chemin a changé
                if ($processedPath !== $currentImagePath && $disk->exists($currentImagePath)) {
                    $disk->delete($currentImagePath);
                }

                // Mettre à jour en base
                $vehicle->timestamps = false;
                $vehicle->update(['image' => $processedPath]);
                $processed++;

            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("  Erreur #{$vehicle->id} — " . Str::limit($e->getMessage(), 80));
                Log::warning('MigrateVehicleImages: error', [
                    'vehicle_id' => $vehicle->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Résumé :");
        $this->line("  ✅ {$processed} photo(s) traitée(s)");
        $this->line("  ❌ {$errors} erreur(s)");

        return self::SUCCESS;
    }
}
