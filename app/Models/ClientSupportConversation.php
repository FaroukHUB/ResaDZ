<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientSupportConversation extends Model
{
    protected $fillable = [
        'client_id',
        'client_email',
        'client_name',
        'subject',
        'category',
        'status',
        'priority',
        'booking_id',
        'last_message_at',
        'client_unread',
        'admin_unread',
        'assigned_to',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'client_unread' => 'boolean',
        'admin_unread' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ClientSupportMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(ClientSupportMessage::class)->latest()->limit(1);
    }

    /**
     * Add a message to the conversation.
     */
    public function addMessage(string $content, string $senderType, ?int $senderId = null, bool $isInternalNote = false): ClientSupportMessage
    {
        $message = $this->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'content' => $content,
            'is_internal_note' => $isInternalNote,
        ]);

        $this->update([
            'last_message_at' => now(),
            'client_unread' => $senderType === 'admin' && !$isInternalNote,
            'admin_unread' => $senderType === 'client',
        ]);

        return $message;
    }

    /**
     * Mark as read by client.
     */
    public function markAsReadByClient(): void
    {
        $this->update(['client_unread' => false]);
        $this->messages()
            ->where('sender_type', 'admin')
            ->where('is_internal_note', false)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Mark as read by admin.
     */
    public function markAsReadByAdmin(): void
    {
        $this->update(['admin_unread' => false]);
        $this->messages()
            ->where('sender_type', 'client')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'booking' => 'Réservation',
            'payment' => 'Paiement',
            'complaint' => 'Réclamation',
            'other' => 'Autre',
            default => 'Général',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Ouvert',
            'pending' => 'En attente',
            'resolved' => 'Résolu',
            'closed' => 'Fermé',
            default => $this->status,
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'warning',
            'pending' => 'info',
            'resolved' => 'success',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get priority color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Scope for open conversations.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'pending']);
    }

    /**
     * Get categories for forms.
     */
    public static function getCategories(): array
    {
        return [
            'general' => 'Général',
            'booking' => 'Réservation',
            'payment' => 'Paiement',
            'complaint' => 'Réclamation',
            'other' => 'Autre',
        ];
    }
}
