<?php

namespace App\Models;

use App\Traits\HasWebpImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Loueur extends Model
{
    use HasFactory, HasWebpImages, Notifiable;

    public function getWebpImageFields(): array
    {
        return ['logo', 'cover_image'];
    }

    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'subdomain',
        'description',
        'logo',
        'cover_image',
        'phone',
        'whatsapp',
        'email_contact',
        'address',
        'city',
        'wilaya',
        'facebook',
        'instagram',
        'tiktok',
        'payment_methods',
        'paypal_email',
        'iban',
        'wise_email',
        'baridimob_rip',
        'is_active',
        'is_verified',
        'verified_at',
        'onboarding_completed_at',
        'onboarding_step',
        'subscription_plan',
        'subscription_expires_at',
        'rating',
        'total_reviews',
        'total_rentals',
        'meta_title',
        'meta_description',
        'trial_ends_at',
        'is_suspended',
        'suspension_reason',
        'commission_paid_until',
        'commission_notes',
        'account_type',
        'offers_transfer',
        'offers_delivery',
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_suspended' => 'boolean',
        'offers_transfer' => 'boolean',
        'offers_delivery' => 'boolean',
        'verified_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
        'onboarding_step' => 'integer',
        'subscription_expires_at' => 'datetime',
        'trial_ends_at' => 'date',
        'commission_paid_until' => 'date',
        'rating' => 'decimal:2',
    ];

    const ONBOARDING_STEPS = [
        1 => 'profile',
        2 => 'zones',
        3 => 'reservations',
        4 => 'options',
        5 => 'conditions',
        6 => 'badges',
        7 => 'notifications',
    ];

    /**
     * Check if onboarding is completed.
     */
    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    /**
     * Get onboarding progress percentage.
     */
    public function getOnboardingProgress(): int
    {
        $totalSteps = count(self::ONBOARDING_STEPS);
        return $totalSteps > 0 ? round(($this->onboarding_step / $totalSteps) * 100) : 0;
    }

    /**
     * Mark onboarding as completed.
     */
    public function completeOnboarding(): void
    {
        $this->update([
            'onboarding_completed_at' => now(),
            'onboarding_step' => count(self::ONBOARDING_STEPS),
        ]);
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingConversations(): HasMany
    {
        return $this->hasMany(BookingConversation::class);
    }

    public function deliveryZones(): HasMany
    {
        return $this->hasMany(DeliveryZone::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(LoueurSetting::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function transferRoutes(): HasMany
    {
        return $this->hasMany(TransferRoute::class);
    }

    public function transferBookings(): HasMany
    {
        return $this->hasMany(TransferBooking::class);
    }

    public function deliveryRates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class);
    }

    public function deliveryBookings(): HasMany
    {
        return $this->hasMany(DeliveryBooking::class);
    }

    public function isTaxi(): bool
    {
        return $this->account_type === 'taxi';
    }

    public function isLoueur(): bool
    {
        return $this->account_type === 'loueur';
    }

    /**
     * Get the wilaya codes for this loueur.
     */
    public function getWilayaCodes(): array
    {
        return DB::table('loueur_wilaya')
            ->where('loueur_id', $this->id)
            ->pluck('wilaya_code')
            ->toArray();
    }

    /**
     * Get the wilaya names for this loueur.
     */
    public function getWilayaNames(): array
    {
        $codes = $this->getWilayaCodes();
        $wilayas = config('resadz.wilayas', []);

        return array_map(fn($code) => $wilayas[$code] ?? $code, $codes);
    }

    /**
     * Get the wilayas as a formatted string.
     */
    public function getWilayasString(): string
    {
        return implode(', ', $this->getWilayaNames());
    }

    /**
     * Sync the wilayas for this loueur.
     */
    public function syncWilayas(array $wilayaCodes): void
    {
        DB::table('loueur_wilaya')->where('loueur_id', $this->id)->delete();

        foreach ($wilayaCodes as $code) {
            DB::table('loueur_wilaya')->insert([
                'loueur_id' => $this->id,
                'wilaya_code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Check if loueur operates in a specific wilaya.
     */
    public function operatesInWilaya(string $wilayaCode): bool
    {
        return in_array($wilayaCode, $this->getWilayaCodes());
    }

    /**
     * Scope to filter loueurs by wilaya.
     */
    public function scopeInWilaya($query, string $wilayaCode)
    {
        return $query->whereExists(function ($q) use ($wilayaCode) {
            $q->select(DB::raw(1))
                ->from('loueur_wilaya')
                ->whereColumn('loueur_wilaya.loueur_id', 'loueurs.id')
                ->where('loueur_wilaya.wilaya_code', $wilayaCode);
        });
    }

    /**
     * Scope to filter loueurs by multiple wilayas.
     */
    public function scopeInWilayas($query, array $wilayaCodes)
    {
        return $query->whereExists(function ($q) use ($wilayaCodes) {
            $q->select(DB::raw(1))
                ->from('loueur_wilaya')
                ->whereColumn('loueur_wilaya.loueur_id', 'loueurs.id')
                ->whereIn('loueur_wilaya.wilaya_code', $wilayaCodes);
        });
    }

    public function unreadConversationsCount(): int
    {
        return $this->conversations()->where('loueur_unread', true)->count();
    }

    public function unpaidInvoicesCount(): int
    {
        return $this->invoices()->whereIn('status', ['sent', 'overdue'])->count();
    }

    // Helpers pour récupérer les settings
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'decimal' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public function setSetting(string $key, $value, string $type = 'string'): void
    {
        $this->settings()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $type === 'json' ? json_encode($value) : (string) $value,
                'type' => $type,
            ]
        );
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): ?string
    {
        // Utiliser l'email de contact du loueur, sinon l'email de l'utilisateur associé
        return $this->email_contact ?: ($this->user?->email);
    }

    /**
     * Get configured rental conditions.
     */
    public function getConditions(): array
    {
        $conditions = $this->getSetting('rental_conditions', []);
        if (!is_array($conditions)) {
            return [];
        }

        return collect($conditions)->map(function ($condition) {
            return [
                'title' => $condition['title'] === 'Autre'
                    ? ($condition['custom_title'] ?? 'Condition')
                    : $condition['title'],
                'description' => $condition['description'] ?? '',
            ];
        })->toArray();
    }

    /**
     * Get configured badge labels for vehicle cards.
     */
    public function getBadges(): array
    {
        $badges = [];

        if ($this->getSetting('badge_insurance', false)) {
            $badges[] = ['icon' => 'check', 'text' => 'Assurance incluse', 'color' => 'green'];
        }
        if ($this->getSetting('badge_delivery', false)) {
            $badges[] = ['icon' => 'truck', 'text' => 'Livraison offerte', 'color' => 'blue'];
        }
        if ($this->getSetting('badge_degressive', false)) {
            $badges[] = ['icon' => 'arrow-down', 'text' => 'Prix dégressif selon la durée', 'color' => 'amber'];
        }
        if ($this->getSetting('badge_airport', false)) {
            $badges[] = ['icon' => 'plane', 'text' => 'Livraison aéroport', 'color' => 'blue'];
        }
        if ($this->getSetting('badge_km_unlimited', false)) {
            $badges[] = ['icon' => 'infinity', 'text' => 'Kilométrage illimité', 'color' => 'green'];
        }

        $customBadges = $this->getSetting('custom_badges', []);
        if (is_array($customBadges)) {
            foreach ($customBadges as $custom) {
                if (!empty($custom['text'])) {
                    $badges[] = ['icon' => 'star', 'text' => $custom['text'], 'color' => 'amber'];
                }
            }
        }

        return $badges;
    }

    /**
     * Check if loueur is in trial period.
     */
    public function isInTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if trial has expired.
     */
    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Get total unpaid commission for a given period.
     */
    public function getUnpaidCommission(?string $month = null): float
    {
        $query = $this->bookings()
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->where('commission_paid', false);

        if ($month) {
            $query->whereMonth('created_at', substr($month, 5, 2))
                  ->whereYear('created_at', substr($month, 0, 4));
        }

        return $query->sum('commission_amount');
    }

    /**
     * Get count of bookings with unpaid commission.
     */
    public function getUnpaidBookingsCount(?string $month = null): int
    {
        $query = $this->bookings()
            ->whereIn('status', ['confirmed', 'active', 'completed'])
            ->where('commission_paid', false);

        if ($month) {
            $query->whereMonth('created_at', substr($month, 5, 2))
                  ->whereYear('created_at', substr($month, 0, 4));
        }

        return $query->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_suspended', false);
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    public function scopeInTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now());
    }

    public function scopeTrialExpired($query)
    {
        return $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '<=', now());
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeBySubdomain($query, string $subdomain)
    {
        return $query->where('subdomain', $subdomain);
    }
}
