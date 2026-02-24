<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingMessage extends Model
{
    protected $fillable = [
        'booking_conversation_id',
        'sender_type',
        'sender_id',
        'content',
        'content_filtered',
        'has_filtered_content',
        'read_at',
    ];

    protected $casts = [
        'has_filtered_content' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BookingMessage $message) {
            $message->filterContent();
        });

        static::created(function (BookingMessage $message) {
            $message->conversation->update([
                'last_message_at' => $message->created_at,
            ]);

            // Increment unread count for the other party
            if ($message->sender_type === 'loueur') {
                $message->conversation->increment('client_unread_count');
            } else {
                $message->conversation->increment('loueur_unread_count');
            }
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(BookingConversation::class, 'booking_conversation_id');
    }

    public function sender(): BelongsTo
    {
        if ($this->sender_type === 'loueur') {
            return $this->belongsTo(Loueur::class, 'sender_id');
        }

        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Filter phone numbers and emails from message content.
     */
    public function filterContent(): void
    {
        $content = $this->content;
        $filtered = $content;

        // Algerian phone numbers: 05XX, 06XX, 07XX (with various formats)
        $phonePatterns = [
            // Standard formats: 0555123456, 0555 12 34 56, 0555-12-34-56
            '/\b0[567]\s*\d{2}[\s\-\.]?\d{2}[\s\-\.]?\d{2}[\s\-\.]?\d{2}\b/',
            // With +213: +213555123456, +213 555 12 34 56
            '/\+213\s*[567]\s*\d{2}[\s\-\.]?\d{2}[\s\-\.]?\d{2}[\s\-\.]?\d{2}\b/',
            // Written out: zero cinq, zéro six, etc.
            '/\b(z[ée]ro|zero)\s*(cinq|six|sept)\b/i',
            // Spaced numbers to avoid detection: 0 5 5 5 1 2 3 4 5 6
            '/\b0\s*[567](\s*\d){8}\b/',
        ];

        foreach ($phonePatterns as $pattern) {
            $filtered = preg_replace($pattern, '[numéro masqué]', $filtered);
        }

        // Email addresses
        $filtered = preg_replace(
            '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            '[email masqué]',
            $filtered
        );

        // Common email obfuscation: name [at] domain [dot] com
        $filtered = preg_replace(
            '/[a-zA-Z0-9._%+-]+\s*[\[\(]?\s*(at|arobase|@)\s*[\]\)]?\s*[a-zA-Z0-9.-]+\s*[\[\(]?\s*(dot|point|\.)\s*[\]\)]?\s*[a-zA-Z]{2,}/i',
            '[email masqué]',
            $filtered
        );

        // Check if content was filtered
        if ($filtered !== $content) {
            $this->has_filtered_content = true;
            $this->content_filtered = $filtered;
        }
    }

    /**
     * Get the display content (filtered if applicable).
     */
    public function getDisplayContentAttribute(): string
    {
        return $this->content_filtered ?? $this->content;
    }

    /**
     * Check if message is from loueur.
     */
    public function isFromLoueur(): bool
    {
        return $this->sender_type === 'loueur';
    }

    /**
     * Check if message is from client.
     */
    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    /**
     * Get sender display name.
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'loueur') {
            return $this->conversation->loueur->company_name ?? 'Loueur';
        }

        return $this->conversation->client_display_name;
    }
}
