<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loueur extends Model
{
    use HasFactory;

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
        'subscription_plan',
        'subscription_expires_at',
        'rating',
        'total_reviews',
        'total_rentals',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'rating' => 'decimal:2',
    ];

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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
