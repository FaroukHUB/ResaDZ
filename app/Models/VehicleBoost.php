<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleBoost extends Model
{
    protected $fillable = [
        'vehicle_id',
        'loueur_id',
        'boost_package_id',
        'starts_at',
        'ends_at',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function boostPackage(): BelongsTo
    {
        return $this->belongsTo(BoostPackage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at <= now()
            && $this->ends_at >= now();
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($this->boostPackage->duration_days),
        ]);
    }

    public function getRemainingDaysAttribute(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->ends_at, false));
    }
}
