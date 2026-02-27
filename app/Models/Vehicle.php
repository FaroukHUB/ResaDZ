<?php

namespace App\Models;

use App\Traits\HasWebpImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Setting;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, HasWebpImages;

    /**
     * Image fields to convert to WebP
     */
    public function getWebpImageFields(): array
    {
        return ['image'];
    }

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
        'degressive_pricing',
        'deposit_amount',
        'deposit_amount_eur',
        'deposit_currency',
        'available_options',
        'transmission',
        'fuel_type',
        'has_air_conditioning',
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
        'is_in_selection',
        'selection_order',
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
        'degressive_pricing' => 'array',
        'deposit_amount' => 'decimal:2',
        'deposit_amount_eur' => 'decimal:2',
        'available_options' => 'array',
        'mileage_limit_per_day' => 'integer',
        'has_air_conditioning' => 'boolean',
        'extra_mileage_fee' => 'decimal:2',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_in_selection' => 'boolean',
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

    public function offers(): HasMany
    {
        return $this->hasMany(VehicleOffer::class);
    }

    public function boosts(): HasMany
    {
        return $this->hasMany(VehicleBoost::class);
    }

    /**
     * Get the active boost for this vehicle.
     */
    public function getActiveBoostAttribute(): ?VehicleBoost
    {
        return $this->boosts()
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->first();
    }

    /**
     * Check if this vehicle is currently boosted.
     */
    public function getIsBoostedAttribute(): bool
    {
        return $this->boosts()
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->exists();
    }

    /**
     * Get the currently active offer for this vehicle.
     */
    public function getActiveOfferAttribute(): ?VehicleOffer
    {
        return $this->offers()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
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

    public function scopeInSelection($query)
    {
        return $query->where('is_in_selection', true)
            ->orderBy('selection_order')
            ->orderBy('created_at', 'desc');
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

    // Calculer le prix selon la durée avec prix dégressif et commission
    public function calculatePrice(int $days, string $currency = 'DZD'): array
    {
        $pricePerDay = $currency === 'EUR'
            ? ($this->price_per_day_eur ?? 0)
            : $this->price_per_day;

        // Chercher le prix dégressif applicable
        if ($this->degressive_pricing && is_array($this->degressive_pricing)) {
            // Trier par from_days descendant pour prendre le meilleur palier
            $tiers = collect($this->degressive_pricing)
                ->filter(fn($tier) => isset($tier['from_days']) && $days >= $tier['from_days'])
                ->sortByDesc('from_days')
                ->first();

            if ($tiers) {
                $pricePerDay = $currency === 'EUR'
                    ? ($tiers['price_per_day_eur'] ?? $pricePerDay)
                    : ($tiers['price_per_day'] ?? $pricePerDay);
            }
        }

        // Prix du loueur (ce qu'il affiche)
        $loueurGrossTotal = $pricePerDay * $days;

        // Commission ResaDZ prélevée au loueur (uniquement en DZD)
        $loueurCommissionPerDay = $currency === 'EUR' ? 0 : (int) Setting::get('loueur_commission_per_day_dzd', 150);
        $loueurCommissionTotal = $loueurCommissionPerDay * $days;

        // Ce que le loueur reçoit réellement
        $loueurNetTotal = $loueurGrossTotal - $loueurCommissionTotal;

        // Frais de service facturés au client (uniquement en DZD)
        $clientServiceFeePerDay = $currency === 'EUR' ? 0 : (int) Setting::get('client_service_fee_per_day_dzd', 100);
        $clientServiceFeeTotal = $clientServiceFeePerDay * $days;

        // Prix affiché au client (prix loueur + frais de service)
        $clientTotal = $loueurGrossTotal + $clientServiceFeeTotal;

        return [
            'price_per_day_loueur' => $pricePerDay,
            'price_per_day_client' => $pricePerDay + $clientServiceFeePerDay,
            'loueur_gross_total' => $loueurGrossTotal,
            'loueur_commission_per_day' => $loueurCommissionPerDay,
            'loueur_commission_total' => $loueurCommissionTotal,
            'loueur_net_total' => $loueurNetTotal,
            'client_service_fee_per_day' => $clientServiceFeePerDay,
            'client_service_fee_total' => $clientServiceFeeTotal,
            'client_total' => $clientTotal,
            'days' => $days,
            'currency' => $currency,
        ];
    }

    // Prix affiché au client (avec frais de service)
    public function getClientPricePerDay(string $currency = 'DZD'): float
    {
        $basePrice = $currency === 'EUR'
            ? ($this->price_per_day_eur ?? 0)
            : $this->price_per_day;

        return $currency === 'EUR'
            ? $basePrice
            : $basePrice + (int) Setting::get('client_service_fee_per_day_dzd', 100);
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    public function getFormattedPriceAttribute(): string
    {
        $clientPrice = $this->getClientPricePerDay('DZD');
        return number_format($clientPrice, 0, ',', ' ') . ' DA';
    }

    public function getFormattedPriceEurAttribute(): string
    {
        if (!$this->price_per_day_eur) {
            return '';
        }
        return number_format($this->price_per_day_eur, 0, ',', ' ') . ' €';
    }

    public function isAvailableForDates($startDate, $endDate): bool
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        // Vérifier available_from / available_until
        if ($this->available_from && $start->lt($this->available_from)) {
            return false;
        }
        if ($this->available_until && $end->gt($this->available_until)) {
            return false;
        }

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
