<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'loueur_id',
        'subject',
        'status',
        'priority',
        'category',
        'last_message_at',
        'loueur_unread',
        'admin_unread',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'loueur_unread' => 'boolean',
        'admin_unread' => 'boolean',
    ];

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_ARCHIVED = 'archived';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Category constants
    const CATEGORY_BOOST = 'boost';
    const CATEGORY_INVOICE = 'invoice';
    const CATEGORY_SUPPORT = 'support';
    const CATEGORY_GENERAL = 'general';

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeWithUnreadForAdmin($query)
    {
        return $query->where('admin_unread', true);
    }

    public function scopeWithUnreadForLoueur($query)
    {
        return $query->where('loueur_unread', true);
    }

    public function markAsReadByAdmin(): void
    {
        $this->update(['admin_unread' => false]);
        $this->messages()
            ->where('sender_type', 'loueur')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAsReadByLoueur(): void
    {
        $this->update(['loueur_unread' => false]);
        $this->messages()
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function addMessage(string $content, string $senderType, ?int $senderId = null, array $attachments = [], bool $isSystemMessage = false): Message
    {
        $message = $this->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'content' => $content,
            'attachments' => $attachments ?: null,
            'is_system_message' => $isSystemMessage,
        ]);

        // Update conversation
        $this->update([
            'last_message_at' => now(),
            'loueur_unread' => $senderType === 'admin',
            'admin_unread' => $senderType === 'loueur',
        ]);

        return $message;
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_BOOST => 'Boost / Sponsoring',
            self::CATEGORY_INVOICE => 'Facturation',
            self::CATEGORY_SUPPORT => 'Support technique',
            self::CATEGORY_GENERAL => 'Général',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Basse',
            self::PRIORITY_NORMAL => 'Normale',
            self::PRIORITY_HIGH => 'Haute',
            self::PRIORITY_URGENT => 'Urgente',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN => 'Ouvert',
            self::STATUS_CLOSED => 'Fermé',
            self::STATUS_ARCHIVED => 'Archivé',
        ];
    }
}
