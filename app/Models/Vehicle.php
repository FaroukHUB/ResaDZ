<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loueur_id',
        'brand_id',
        'category_id',
        'model',
        'full_name',
        'slug',
        'price_per_day',
        'price_per_day_eur',
        'price_per_week',
        'price_per_month',
        'pricing',
        'deposit_amount',
        'deposit_currency',
        'available_options',
        'transmission',
        'fuel_type',
        'seats',
        'doors',
        'luggage_capacity',
        'year',
        'mileage',
        'mileage_limit_per_day',
        'extra_mileage_fee',
        'color',
        'features',
        'image',
        'gallery',
        'status',
        'is_featured',
        'is_active',
        'available_from',
        'available_until',
        'min_rental_days',
        'max_rental_days',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'price_per_day_eur' => 'decimal:2',
        'price_per_week' => 'decimal:2',
        'price_per_month' => 'decimal:2',
        'pricing' => 'array',
        'deposit_amount' => 'decimal:2',
        'available_options' => 'array',
        'mileage_limit_per_day' => 'integer',
        'extra_mileage_fee' => 'decimal:2',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'available_from' => 'date',
        'available_until' => 'date',
        'min_rental_days' => 'integer',
        'max_rental_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vehicle) {
            if (empty($vehicle->slug)) {
                $vehicle->slug = Str::slug($vehicle->full_name);
            }
        });
    }

    // Relations
    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByBrand($query, $brandSlug)
    {
        return $query->whereHas('brand', function ($q) use ($brandSlug) {
            $q->where('slug', $brandSlug);
        });
    }

    public function scopeForLoueur($query, int $loueurId)
    {
        return $query->where('loueur_id', $loueurId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('full_name');
    }

    // Calculer le prix selon la durée (config du loueur)
    public function calculatePrice(int $days): array
    {
        $basePrice = $this->price_per_day * $days;
        $discount = 0;

        // Appliquer les réductions par durée si configurées
        if ($this->pricing && isset($this->pricing['by_duration'])) {
            foreach ($this->pricing['by_duration'] as $rule) {
                $minDays = $rule['min_days'] ?? 0;
                $maxDays = $rule['max_days'] ?? PHP_INT_MAX;

                if ($days >= $minDays && $days <= $maxDays) {
                    $discountPercent = $rule['discount_percent'] ?? 0;
                    $discount = $basePrice * ($discountPercent / 100);
                    break;
                }
            }
        }

        return [
            'base_price' => $basePrice,
            'discount' => $discount,
            'total' => $basePrice - $discount,
            'currency' => 'DZD',
        ];
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_per_day, 0, ',', ' ') . ' DA';
    }

    public function isAvailableForDates($startDate, $endDate): bool
    {
        // Vérifier s'il n'y a pas de blocage sur ces dates
        $hasBlocking = $this->availabilities()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        return !$hasBlocking && $this->isAvailable();
    }
}
