<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Services\VehicleImageProcessingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateVehicleImages extends Command
{
    protected $signature = 'vehicles:migrate-images';

    protected $description = 'Traiter toutes les photos principales de véhicules existants via l\'API showroom';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $service = new VehicleImageProcessingService();

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
        $skipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($vehicles->count());
        $bar->start();

        foreach ($vehicles as $vehicle) {
            $imagePath = $vehicle->image;

            // Vérifier que le fichier existe
            if (!$disk->exists($imagePath)) {
                $this->newLine();
                $this->warn("  Skip #{$vehicle->id} ({$vehicle->full_name}) — fichier introuvable: {$imagePath}");
                $errors++;
                $bar->advance();
                continue;
            }

            // Vérifier si déjà traité (original existe dans originals/)
            $filename = pathinfo($imagePath, PATHINFO_FILENAME);
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $originalPath = 'vehicles/originals/' . $filename . '.' . $extension;

            if ($disk->exists($originalPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Traiter via le service
            $newPath = $service->processMainImage($imagePath);

            if ($newPath !== $imagePath) {
                // L'image a été traitée, mettre à jour en base
                $vehicle->timestamps = false;
                $vehicle->update(['image' => $newPath]);
                $processed++;
            } else {
                // L'API a échoué, l'original est conservé
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Résumé :");
        $this->line("  ✅ {$processed} photo(s) traitée(s)");
        $this->line("  ⏭️  {$skipped} déjà traitée(s) (skip)");
        $this->line("  ❌ {$errors} erreur(s)");

        return self::SUCCESS;
    }
}
