<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoostPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration_days',
        'price',
        'boost_type',
        'position_boost',
        'show_badge',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'show_badge' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function vehicleBoosts(): HasMany
    {
        return $this->hasMany(VehicleBoost::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' DA';
    }
}
