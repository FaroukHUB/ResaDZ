<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSupportMessage extends Model
{
    protected $fillable = [
        'client_support_conversation_id',
        'sender_type',
        'sender_id',
        'content',
        'attachments',
        'is_internal_note',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal_note' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ClientSupportConversation::class, 'client_support_conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Check if message is from client.
     */
    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    /**
     * Check if message is from admin.
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    /**
     * Get sender display name.
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'admin') {
            return $this->sender?->name ?? 'Support ResaDZ';
        }

        return $this->conversation->client_name;
    }
}
