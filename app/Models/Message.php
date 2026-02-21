<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'content',
        'attachments',
        'is_system_message',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_system_message' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        if ($this->sender_type === 'admin') {
            return $this->belongsTo(User::class, 'sender_id');
        }
        return $this->belongsTo(Loueur::class, 'sender_id');
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->is_system_message) {
            return 'Système';
        }

        if ($this->sender_type === 'admin') {
            return 'Admin ResaDZ';
        }

        return $this->conversation->loueur->company_name ?? 'Loueur';
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    public function isFromLoueur(): bool
    {
        return $this->sender_type === 'loueur';
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }
}
