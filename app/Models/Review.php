<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'loueur_id',
        'type',
        'reviewer_id',
        'reviewed_user_id',
        'rating_overall',
        'rating_vehicle',
        'rating_communication',
        'rating_punctuality',
        'rating_cleanliness',
        'rating_respect',
        'comment',
        'is_public',
        'is_approved',
        'is_flagged',
        'flag_reason',
        'response',
        'responded_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_approved' => 'boolean',
        'is_flagged' => 'boolean',
        'responded_at' => 'datetime',
    ];

    // Relations
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    // Helpers
    public function isClientToLoueur(): bool
    {
        return $this->type === 'client_to_loueur';
    }

    public function isLoueurToClient(): bool
    {
        return $this->type === 'loueur_to_client';
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeClientToLoueur($query)
    {
        return $query->where('type', 'client_to_loueur');
    }

    public function scopeLoueurToClient($query)
    {
        return $query->where('type', 'loueur_to_client');
    }
}
