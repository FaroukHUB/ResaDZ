<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewsletterSend extends Model
{
    protected $fillable = [
        'newsletter_id',
        'subscriber_id',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'tracking_token',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($send) {
            if (empty($send->tracking_token)) {
                $send->tracking_token = Str::random(32);
            }
        });
    }

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscriber::class, 'subscriber_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->newsletter->increment('sent_count');
    }

    public function markAsOpened(): void
    {
        if ($this->status === 'sent') {
            $this->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);

            $this->newsletter->incrementOpened();
        }
    }

    public function markAsClicked(): void
    {
        if (in_array($this->status, ['sent', 'opened'])) {
            $this->update([
                'status' => 'clicked',
                'clicked_at' => now(),
            ]);

            if ($this->opened_at === null) {
                $this->update(['opened_at' => now()]);
                $this->newsletter->incrementOpened();
            }

            $this->newsletter->incrementClicked();
        }
    }

    public function markAsBounced(): void
    {
        $this->update(['status' => 'bounced']);
        $this->newsletter->incrementBounced();
    }

    public function getTrackingPixelUrlAttribute(): string
    {
        return route('newsletter.track.open', ['token' => $this->tracking_token]);
    }

    public function getTrackingClickUrlAttribute(): string
    {
        return route('newsletter.track.click', ['token' => $this->tracking_token]);
    }
}
