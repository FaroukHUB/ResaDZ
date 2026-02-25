<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRate extends Model
{
    protected $fillable = [
        'loueur_id',
        'from_city',
        'to_city',
        'package_type',
        'base_price',
        'price_per_kg',
        'max_weight',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPackageTypeLabel(): string
    {
        return match ($this->package_type) {
            'colis' => 'Colis',
            'document' => 'Document',
            'repas' => 'Repas',
            default => $this->package_type,
        };
    }
}
