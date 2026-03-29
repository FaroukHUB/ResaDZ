<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleImageProcessingService
{
    private const API_URL = 'http://84.247.191.205:5000/process-image';
    private const API_TOKEN = 'resadz_vps_secret_2026';
    private const ORIGINALS_DIR = 'vehicles/originals';

    /**
     * Process the main vehicle image through the external API.
     * Saves the original in vehicles/originals/ and replaces with the processed image.
     * Falls back to the original if the API is unavailable.
     */
    public function processMainImage(string $imagePath): string
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($imagePath)) {
            Log::warning('VehicleImageProcessing: image not found on disk', ['path' => $imagePath]);
            return $imagePath;
        }

        try {
            $fullPath = $disk->path($imagePath);
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            $filename = pathinfo($imagePath, PATHINFO_FILENAME);

            // 1. Sauvegarder l'original dans vehicles/originals/
            $originalPath = self::ORIGINALS_DIR . '/' . $filename . '.' . $extension;
            $disk->copy($imagePath, $originalPath);

            // 2. Envoyer à l'API externe
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Token' => self::API_TOKEN,
                ])
                ->attach('image', file_get_contents($fullPath), $filename . '.' . $extension)
                ->post(self::API_URL);

            if (!$response->successful()) {
                Log::warning('VehicleImageProcessing: API returned error', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 200),
                    'path' => $imagePath,
                ]);
                return $imagePath;
            }

            // 3. Récupérer l'image traitée et remplacer l'originale
            $processedContent = $response->body();

            if (empty($processedContent) || strlen($processedContent) < 1000) {
                Log::warning('VehicleImageProcessing: API returned empty or too small response', [
                    'size' => strlen($processedContent),
                    'path' => $imagePath,
                ]);
                return $imagePath;
            }

            // Sauvegarder l'image traitée (en JPEG) à la place de l'originale
            $processedPath = 'vehicles/' . $filename . '.jpg';
            $disk->put($processedPath, $processedContent);

            // Supprimer l'ancien fichier si le chemin a changé
            if ($processedPath !== $imagePath && $disk->exists($imagePath)) {
                $disk->delete($imagePath);
            }

            Log::info('VehicleImageProcessing: image processed successfully', [
                'original' => $originalPath,
                'processed' => $processedPath,
            ]);

            return $processedPath;

        } catch (\Exception $e) {
            Log::warning('VehicleImageProcessing: API unavailable, keeping original', [
                'error' => $e->getMessage(),
                'path' => $imagePath,
            ]);
            return $imagePath;
        }
    }
}
