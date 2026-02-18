<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'confirmation_token',
        'loueur_id',
        'vehicle_id',
        'client_id',
        'start_date',
        'end_date',
        'total_days',
        'pickup_zone_id',
        'return_zone_id',
        'pickup_address',
        'pickup_time',
        'return_address',
        'return_time',
        'pickup_notes',
        'return_notes',
        'base_price',
        'duration_discount',
        'season_surcharge',
        'options_total',
        'delivery_fee',
        'return_fee',
        'extra_fees',
        'discount_amount',
        'total_price',
        'currency',
        'selected_options',
        'deposit_amount',
        'deposit_currency',
        'deposit_status',
        'deposit_notes',
        'advance_amount',
        'advance_status',
        'advance_payment_method',
        'advance_paid_at',
        'advance_expires_at',
        'amount_paid',
        'amount_remaining',
        'payment_method',
        'payment_status',
        'client_name',
        'client_phone',
        'client_email',
        'client_whatsapp',
        'client_id_document',
        'client_license_front',
        'client_license_back',
        'client_selfie',
        'photos_before',
        'photos_after',
        'condition_notes_before',
        'condition_notes_after',
        'mileage_start',
        'mileage_end',
        'fuel_level_start',
        'fuel_level_end',
        'contract_signed_at',
        'contract_signature',
        'contract_pdf',
        'confirmed_by_loueur_at',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'client_reviewed',
        'loueur_reviewed',
        'internal_notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'selected_options' => 'array',
        'photos_before' => 'array',
        'photos_after' => 'array',
        'advance_paid_at' => 'datetime',
        'advance_expires_at' => 'datetime',
        'contract_signed_at' => 'datetime',
        'confirmed_by_loueur_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'client_reviewed' => 'boolean',
        'loueur_reviewed' => 'boolean',
        'base_price' => 'decimal:2',
        'duration_discount' => 'decimal:2',
        'season_surcharge' => 'decimal:2',
        'options_total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'return_fee' => 'decimal:2',
        'extra_fees' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->reference)) {
                $booking->reference = 'RDZ-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
            if (empty($booking->confirmation_token)) {
                $booking->confirmation_token = Str::random(64);
            }
        });
    }

    // Relations
    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function pickupZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'pickup_zone_id');
    }

    public function returnZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'return_zone_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Helpers
    public function getClientDisplayName(): string
    {
        if ($this->client) {
            return $this->client->name;
        }
        return $this->client_name ?? 'Client inconnu';
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending'
            && $this->advance_expires_at
            && $this->advance_expires_at->isPast();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function getFormattedTotal(): string
    {
        $symbol = $this->currency === 'EUR' ? '€' : 'DA';
        return number_format($this->total_price, 0, ',', ' ') . ' ' . $symbol;
    }

    public function getConfirmationUrl(): string
    {
        return route('booking.client-confirmation', $this->confirmation_token);
    }

    public function isConfirmedByLoueur(): bool
    {
        return $this->confirmed_by_loueur_at !== null;
    }

    // Scopes
    public function scopeForLoueur($query, int $loueurId)
    {
        return $query->where('loueur_id', $loueurId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['confirmed'])
            ->where('start_date', '>=', now());
    }
}
