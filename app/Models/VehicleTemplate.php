<?php

namespace App\Models;

use App\Traits\HasWebpImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTemplate extends Model
{
    use HasWebpImages;

    protected $fillable = [
        'brand_id',
        'model_name',
        'color',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getWebpImageFields(): array
    {
        return ['image_path'];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getColorLabelAttribute(): string
    {
        return match($this->color) {
            'noir' => 'Noir',
            'blanc' => 'Blanc',
            'gris' => 'Gris',
            'rouge' => 'Rouge',
            'bleu' => 'Bleu',
            'vert' => 'Vert',
            'beige' => 'Beige',
            'marron' => 'Marron',
            'orange' => 'Orange',
            'jaune' => 'Jaune',
            default => ucfirst($this->color),
        };
    }
}
