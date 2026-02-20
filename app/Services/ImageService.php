<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Convert an uploaded file to WebP format
     */
    public static function convertToWebp(string $path, int $quality = 85): ?string
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return null;
        }

        $fullPath = $disk->path($path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Skip if already WebP
        if ($extension === 'webp') {
            return $path;
        }

        // Skip non-image files
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            return $path;
        }

        try {
            // Create image resource based on type
            $image = match ($extension) {
                'jpg', 'jpeg' => imagecreatefromjpeg($fullPath),
                'png' => imagecreatefrompng($fullPath),
                'gif' => imagecreatefromgif($fullPath),
                'bmp' => imagecreatefrombmp($fullPath),
                default => null,
            };

            if (!$image) {
                return $path;
            }

            // Handle transparency for PNG
            if ($extension === 'png') {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }

            // Generate new WebP path
            $newPath = preg_replace('/\.[^.]+$/', '.webp', $path);
            $newFullPath = $disk->path($newPath);

            // Ensure directory exists
            $directory = dirname($newFullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Convert to WebP
            $success = imagewebp($image, $newFullPath, $quality);
            imagedestroy($image);

            if ($success) {
                // Delete original file
                $disk->delete($path);
                return $newPath;
            }

            return $path;
        } catch (\Exception $e) {
            \Log::warning('WebP conversion failed: ' . $e->getMessage());
            return $path;
        }
    }

    /**
     * Process and convert an uploaded file to WebP
     */
    public static function processUpload(UploadedFile $file, string $directory, int $quality = 85): string
    {
        // Generate unique filename
        $filename = Str::uuid() . '.webp';
        $path = $directory . '/' . $filename;

        try {
            $extension = strtolower($file->getClientOriginalExtension());

            // If already WebP, just store it
            if ($extension === 'webp') {
                return $file->storeAs($directory, $filename, 'public');
            }

            // Create image resource
            $image = match ($extension) {
                'jpg', 'jpeg' => imagecreatefromjpeg($file->getPathname()),
                'png' => imagecreatefrompng($file->getPathname()),
                'gif' => imagecreatefromgif($file->getPathname()),
                'bmp' => imagecreatefrombmp($file->getPathname()),
                default => null,
            };

            if (!$image) {
                // Fallback to original upload
                return $file->store($directory, 'public');
            }

            // Handle transparency
            if ($extension === 'png') {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }

            // Ensure directory exists
            $fullDirectory = Storage::disk('public')->path($directory);
            if (!is_dir($fullDirectory)) {
                mkdir($fullDirectory, 0755, true);
            }

            // Save as WebP
            $fullPath = Storage::disk('public')->path($path);
            imagewebp($image, $fullPath, $quality);
            imagedestroy($image);

            return $path;
        } catch (\Exception $e) {
            \Log::warning('WebP upload processing failed: ' . $e->getMessage());
            // Fallback to original upload
            return $file->store($directory, 'public');
        }
    }

    /**
     * Resize and convert to WebP
     */
    public static function resizeAndConvert(
        string $path,
        int $maxWidth = 1920,
        int $maxHeight = 1080,
        int $quality = 85
    ): ?string {
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return null;
        }

        $fullPath = $disk->path($path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            // Create image resource
            $image = match ($extension) {
                'jpg', 'jpeg' => imagecreatefromjpeg($fullPath),
                'png' => imagecreatefrompng($fullPath),
                'gif' => imagecreatefromgif($fullPath),
                'webp' => imagecreatefromwebp($fullPath),
                'bmp' => imagecreatefrombmp($fullPath),
                default => null,
            };

            if (!$image) {
                return $path;
            }

            // Get dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate new dimensions
            $ratio = min($maxWidth / $width, $maxHeight / $height);

            if ($ratio < 1) {
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);

                // Resize
                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency
                imagealphablending($resized, false);
                imagesavealpha($resized, true);

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Generate WebP path
            $newPath = preg_replace('/\.[^.]+$/', '.webp', $path);
            $newFullPath = $disk->path($newPath);

            // Save as WebP
            imagewebp($image, $newFullPath, $quality);
            imagedestroy($image);

            // Delete original if different
            if ($newPath !== $path) {
                $disk->delete($path);
            }

            return $newPath;
        } catch (\Exception $e) {
            \Log::warning('Image resize failed: ' . $e->getMessage());
            return $path;
        }
    }
}
