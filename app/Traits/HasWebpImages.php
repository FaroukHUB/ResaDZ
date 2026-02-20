<?php

namespace App\Traits;

use App\Services\ImageService;

trait HasWebpImages
{
    /**
     * Boot the trait
     */
    public static function bootHasWebpImages(): void
    {
        static::saving(function ($model) {
            $model->convertImagesToWebp();
        });
    }

    /**
     * Get the image fields that should be converted to WebP
     * Override this method in your model to specify fields
     */
    public function getWebpImageFields(): array
    {
        // Default common image fields
        return ['image', 'logo', 'cover_image', 'icon', 'photo', 'avatar'];
    }

    /**
     * Convert all image fields to WebP
     */
    protected function convertImagesToWebp(): void
    {
        foreach ($this->getWebpImageFields() as $field) {
            if ($this->isDirty($field) && $this->$field) {
                $converted = ImageService::convertToWebp($this->$field);
                if ($converted && $converted !== $this->$field) {
                    $this->$field = $converted;
                }
            }
        }
    }
}
