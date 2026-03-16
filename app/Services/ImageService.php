<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Convert an uploaded file to WebP format
     * Supports both storage/app/public and public/ directories
     */
    public static function convertToWebp(string $path, int $quality = 70): ?string
    {
        $disk = Storage::disk('public');

        // Check if file exists in storage/app/public
        if ($disk->exists($path)) {
            $fullPath = $disk->path($path);
            $isInStorage = true;
        }
        // Check if file exists in public/ directory (for legacy assets/)
        elseif (file_exists(public_path($path))) {
            $fullPath = public_path($path);
            $isInStorage = false;
        } else {
            return null;
        }

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

            if ($isInStorage) {
                $newFullPath = $disk->path($newPath);
            } else {
                $newFullPath = public_path($newPath);
            }

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
                if ($isInStorage) {
                    $disk->delete($path);
                } else {
                    @unlink($fullPath);
                }
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
    public static function processUpload(UploadedFile $file, string $directory, int $quality = 70): string
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
     * Generate a thumbnail of an image
     * Returns the path to the thumbnail (creates if doesn't exist)
     */
    public static function thumbnail(string $path, int $width = 400, int $height = 250, int $quality = 70): string
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return $path;
        }

        // Generate thumbnail path
        $pathInfo = pathinfo($path);
        $thumbPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "-{$width}x{$height}.webp";

        // Return if thumbnail already exists
        if ($disk->exists($thumbPath)) {
            return $thumbPath;
        }

        $fullPath = $disk->path($path);
        $extension = strtolower($pathInfo['extension'] ?? '');

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

            // Get original dimensions
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            // Calculate crop dimensions to maintain aspect ratio
            $targetRatio = $width / $height;
            $origRatio = $origWidth / $origHeight;

            if ($origRatio > $targetRatio) {
                // Original is wider, crop width
                $cropHeight = $origHeight;
                $cropWidth = (int) ($origHeight * $targetRatio);
                $cropX = (int) (($origWidth - $cropWidth) / 2);
                $cropY = 0;
            } else {
                // Original is taller, crop height
                $cropWidth = $origWidth;
                $cropHeight = (int) ($origWidth / $targetRatio);
                $cropX = 0;
                $cropY = (int) (($origHeight - $cropHeight) / 2);
            }

            // Create thumbnail
            $thumb = imagecreatetruecolor($width, $height);
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);

            imagecopyresampled(
                $thumb, $image,
                0, 0, $cropX, $cropY,
                $width, $height, $cropWidth, $cropHeight
            );

            imagedestroy($image);

            // Save thumbnail
            $thumbFullPath = $disk->path($thumbPath);
            imagewebp($thumb, $thumbFullPath, $quality);
            imagedestroy($thumb);

            return $thumbPath;
        } catch (\Exception $e) {
            \Log::warning('Thumbnail generation failed: ' . $e->getMessage());
            return $path;
        }
    }

    /**
     * Resize and convert to WebP
     */
    public static function resizeAndConvert(
        string $path,
        int $maxWidth = 1920,
        int $maxHeight = 1080,
        int $quality = 70
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
