<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DeliveryBooking extends Model
{
    /**
     * Commission fixe pour les chauffeurs (taxis) : 10%
     * Prélevée uniquement au chauffeur, le client ne paie rien en plus.
     */
    public const COMMISSION_RATE = 10;

    protected $fillable = [
        'reference',
        'loueur_id',
        'chauffeur_vehicle_id',
        'pickup_address',
        'pickup_city',
        'delivery_address',
        'delivery_city',
        'package_type',
        'package_description',
        'weight',
        'price',
        'commission_rate',
        'commission_amount',
        'commission_paid',
        'commission_paid_at',
        'client_name',
        'client_phone',
        'client_email',
        'client_notes',
        'recipient_name',
        'recipient_phone',
        'pickup_date',
        'pickup_time',
        'status',
        'confirmed_at',
        'picked_up_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'tracking_code',
        'tracking_history',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'weight' => 'decimal:2',
        'price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_paid' => 'boolean',
        'commission_paid_at' => 'date',
        'confirmed_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'tracking_history' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($delivery) {
            if (empty($delivery->reference)) {
                $delivery->reference = 'LIV-' . strtoupper(Str::random(8));
            }
            if (empty($delivery->tracking_code)) {
                $delivery->tracking_code = strtoupper(Str::random(6));
            }

            // Calculer automatiquement la commission (10% fixe)
            $delivery->calculateCommission();
        });

        static::updating(function ($delivery) {
            // Recalculer la commission si le prix change
            if ($delivery->isDirty('price')) {
                $delivery->calculateCommission();
            }

            // Créer une transaction automatique quand le statut passe à "delivered"
            if ($delivery->isDirty('status') && $delivery->status === 'delivered') {
                $delivery->createIncomeTransaction();
            }
        });
    }

    /**
     * Calcule la commission (10% fixe sur le prix).
     */
    public function calculateCommission(): void
    {
        $rate = Setting::get('commission_rate_delivery', self::COMMISSION_RATE);
        $this->commission_rate = $rate;
        $this->commission_amount = round(($this->price ?? 0) * $rate / 100, 2);
    }

    /**
     * Marquer la commission comme payée.
     */
    public function markCommissionPaid(): void
    {
        $this->update([
            'commission_paid' => true,
            'commission_paid_at' => now(),
        ]);
    }

    /**
     * Créer une transaction d'entrée automatique quand la livraison est terminée.
     */
    public function createIncomeTransaction(): void
    {
        // Vérifier qu'une transaction n'existe pas déjà pour cette livraison
        $exists = Transaction::where('loueur_id', $this->loueur_id)
            ->where('description', 'LIKE', '%' . $this->reference . '%')
            ->where('type', 'income')
            ->exists();

        if ($exists) {
            return;
        }

        Transaction::create([
            'loueur_id' => $this->loueur_id,
            'type' => 'income',
            'description' => 'Livraison ' . $this->pickup_city . ' → ' . $this->delivery_city . ' - ' . $this->client_name . ' (' . $this->reference . ')',
            'amount' => $this->price,
            'currency' => 'DZD',
            'payment_method' => 'cash',
            'transaction_date' => now(),
            'status' => 'completed',
        ]);
    }

    /**
     * Obtenir le montant net après commission (ce que le chauffeur reçoit).
     */
    public function getNetAmountAttribute(): float
    {
        return ($this->price ?? 0) - ($this->commission_amount ?? 0);
    }

    /**
     * Formater le prix total.
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price ?? 0, 0, ',', ' ') . ' DA';
    }

    /**
     * Formater la commission.
     */
    public function getFormattedCommission(): string
    {
        return number_format($this->commission_amount ?? 0, 0, ',', ' ') . ' DA';
    }

    /**
     * Formater le montant net (après commission).
     */
    public function getFormattedNetAmount(): string
    {
        return number_format($this->net_amount, 0, ',', ' ') . ' DA';
    }

    public function addTrackingEvent(string $status, ?string $location = null, ?string $note = null): void
    {
        $history = $this->tracking_history ?? [];
        $history[] = [
            'status' => $status,
            'location' => $location,
            'note' => $note,
            'timestamp' => now()->toISOString(),
        ];
        $this->update([
            'tracking_history' => $history,
            'status' => $status,
        ]);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'picked_up' => 'Colis récupéré',
            'in_transit' => 'En cours de livraison',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'picked_up' => 'info',
            'in_transit' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    // Relations
    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function chauffeurVehicle(): BelongsTo
    {
        return $this->belongsTo(ChauffeurVehicle::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeUnpaidCommission($query)
    {
        return $query->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
            ->where('commission_paid', false);
    }
}
