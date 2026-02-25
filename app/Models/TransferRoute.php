<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferRoute extends Model
{
    protected $fillable = [
        'loueur_id',
        'departure',
        'destination',
        'price',
        'vehicle_type',
        'max_passengers',
        'is_active',
        'round_trip',
        'round_trip_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'round_trip_price' => 'decimal:2',
        'is_active' => 'boolean',
        'round_trip' => 'boolean',
    ];

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
