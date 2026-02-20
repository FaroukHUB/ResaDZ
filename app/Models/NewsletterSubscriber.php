<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'user_id',
        'status',
        'confirmation_token',
        'confirmed_at',
        'unsubscribed_at',
        'unsubscribe_reason',
        'preferences',
        'source',
        'ip_address',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'preferences' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class, 'subscriber_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    public static function subscribe(string $email, ?string $name = null, ?string $source = null): self
    {
        $subscriber = static::firstOrNew(['email' => $email]);

        if (!$subscriber->exists) {
            $subscriber->fill([
                'name' => $name,
                'source' => $source,
                'status' => 'pending',
                'confirmation_token' => Str::random(32),
                'ip_address' => request()->ip(),
            ]);
            $subscriber->save();
        }

        return $subscriber;
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'active',
            'confirmed_at' => now(),
            'confirmation_token' => null,
        ]);
    }

    public function unsubscribe(?string $reason = null): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
            'unsubscribe_reason' => $reason,
        ]);
    }

    public function getUnsubscribeLinkAttribute(): string
    {
        return route('newsletter.unsubscribe', ['token' => $this->confirmation_token ?? Str::random(32)]);
    }
}
