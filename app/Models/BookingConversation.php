<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingConversation extends Model
{
    protected $fillable = [
        'booking_id',
        'loueur_id',
        'client_id',
        'client_email',
        'loueur_last_read_at',
        'client_last_read_at',
        'last_message_at',
        'loueur_unread_count',
        'client_unread_count',
    ];

    protected $casts = [
        'loueur_last_read_at' => 'datetime',
        'client_last_read_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BookingMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(BookingMessage::class)->latest()->limit(1);
    }

    /**
     * Get or create a conversation for a booking.
     */
    public static function getOrCreateForBooking(Booking $booking): self
    {
        return static::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'loueur_id' => $booking->loueur_id,
                'client_id' => $booking->client_id,
                'client_email' => $booking->client_email,
            ]
        );
    }

    /**
     * Mark messages as read for a participant.
     */
    public function markAsReadFor(string $participantType): void
    {
        $now = now();

        if ($participantType === 'loueur') {
            $this->update([
                'loueur_last_read_at' => $now,
                'loueur_unread_count' => 0,
            ]);
            $this->messages()
                ->where('sender_type', 'client')
                ->whereNull('read_at')
                ->update(['read_at' => $now]);
        } else {
            $this->update([
                'client_last_read_at' => $now,
                'client_unread_count' => 0,
            ]);
            $this->messages()
                ->where('sender_type', 'loueur')
                ->whereNull('read_at')
                ->update(['read_at' => $now]);
        }
    }

    /**
     * Get client display name.
     */
    public function getClientDisplayNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->name;
        }

        return $this->booking?->client_name ?? 'Client';
    }
}
