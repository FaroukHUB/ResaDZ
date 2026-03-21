<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasPermissions;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPermissions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'whatsapp',
        'avatar',
        'phone_verified',
        'phone_verified_at',
        'id_document',
        'license_front',
        'license_back',
        'documents_verified',
        'documents_verified_at',
        'client_rating',
        'total_bookings',
        'is_blacklisted',
        'blacklist_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone_verified' => 'boolean',
            'phone_verified_at' => 'datetime',
            'documents_verified' => 'boolean',
            'documents_verified_at' => 'datetime',
            'client_rating' => 'decimal:2',
            'is_blacklisted' => 'boolean',
        ];
    }

    // Filament access control
    public function canAccessPanel(Panel $panel): bool
    {
        // Super admin peut accéder à tous les panels
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Panel loueur - tous les utilisateurs avec rôle loueur peuvent accéder
        // Le middleware EnsureUserIsLoueur redirigera les taxis vers /chauffeur
        if ($panel->getId() === 'loueur' && $this->isLoueur()) {
            return true;
        }

        // Panel chauffeur - tous les utilisateurs avec rôle loueur peuvent accéder
        // Le middleware EnsureUserIsChauffeur redirigera les loueurs vers /loueur
        if ($panel->getId() === 'chauffeur' && $this->isLoueur()) {
            return true;
        }

        // Panel admin - utilisateurs avec rôles admin, moderator ou support
        if ($panel->getId() === 'admin' && $this->canAccessAdminPanel()) {
            return true;
        }

        return false;
    }

    // Relations
    public function loueur(): HasOne
    {
        return $this->hasOne(Loueur::class);
    }

    public function bookingsAsClient(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    // Role helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isLoueur(): bool
    {
        return $this->role === 'loueur';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    // Helpers
    public function hasVerifiedDocuments(): bool
    {
        return $this->documents_verified;
    }

    public function isBlacklisted(): bool
    {
        return $this->is_blacklisted;
    }

    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }
}
