<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'qualification_event',
        'referrer_reward',
        'referred_reward',
        'qualified_at',
        'rewarded_at',
    ];

    protected $casts = [
        'referrer_reward' => 'decimal:2',
        'referred_reward' => 'decimal:2',
        'qualified_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    public function qualify(string $event): void
    {
        $this->update([
            'status' => 'qualified',
            'qualification_event' => $event,
            'qualified_at' => now(),
        ]);
    }

    public function reward(float $referrerAmount, float $referredAmount): void
    {
        $this->update([
            'status' => 'rewarded',
            'referrer_reward' => $referrerAmount,
            'referred_reward' => $referredAmount,
            'rewarded_at' => now(),
        ]);

        // Credit the referrer
        $this->referrer->increment('referral_credits', $referrerAmount);
        $this->referrer->increment('referral_count');

        // Credit the referred user
        $this->referred->increment('referral_credits', $referredAmount);
    }
}
