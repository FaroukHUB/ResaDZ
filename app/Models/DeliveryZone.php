<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryZone extends Model
{
    protected $fillable = [
        'loueur_id',
        'name',
        'type',
        'city',
        'wilaya',
        'delivery_fee',
        'return_fee',
        'currency',
        'is_active',
        'delivery_available',
        'return_available',
        'working_hours',
        'sort_order',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'return_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'delivery_available' => 'boolean',
        'return_available' => 'boolean',
        'working_hours' => 'array',
    ];

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedDeliveryFee(): string
    {
        if ($this->delivery_fee === null || $this->delivery_fee == 0) {
            return 'Gratuit';
        }

        $symbol = $this->currency === 'EUR' ? '€' : 'DA';
        return number_format($this->delivery_fee, 0, ',', ' ') . ' ' . $symbol;
    }
}
