<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralReward extends Model
{
    protected $fillable = [
        'name',
        'event',
        'referrer_amount',
        'referred_amount',
        'reward_type',
        'max_uses_per_user',
        'total_max_uses',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'referrer_amount' => 'decimal:2',
        'referred_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public static function getActiveForEvent(string $event): ?self
    {
        return static::active()->forEvent($event)->first();
    }

    public function getFormattedReferrerAmountAttribute(): string
    {
        return match ($this->reward_type) {
            'credit' => number_format($this->referrer_amount, 0) . ' DA',
            'discount_percent' => $this->referrer_amount . '%',
            'discount_fixed' => number_format($this->referrer_amount, 0) . ' DA',
            default => (string) $this->referrer_amount,
        };
    }

    public function getFormattedReferredAmountAttribute(): string
    {
        return match ($this->reward_type) {
            'credit' => number_format($this->referred_amount, 0) . ' DA',
            'discount_percent' => $this->referred_amount . '%',
            'discount_fixed' => number_format($this->referred_amount, 0) . ' DA',
            default => (string) $this->referred_amount,
        };
    }
}
