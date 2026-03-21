<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TransferBooking extends Model
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
        'departure',
        'destination',
        'transfer_date',
        'transfer_time',
        'passengers',
        'luggage_count',
        'price',
        'commission_rate',
        'commission_amount',
        'commission_paid',
        'commission_paid_at',
        'vehicle_type',
        'client_name',
        'client_phone',
        'client_email',
        'client_notes',
        'status',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_paid' => 'boolean',
        'commission_paid_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function ($transfer) {
            if (empty($transfer->reference)) {
                $transfer->reference = 'TRF-' . strtoupper(Str::random(8));
            }

            // Calculer automatiquement la commission (10% fixe)
            $transfer->calculateCommission();
        });

        static::updating(function ($transfer) {
            // Recalculer la commission si le prix change
            if ($transfer->isDirty('price')) {
                $transfer->calculateCommission();
            }

            // Créer une transaction automatique quand le statut passe à "completed"
            if ($transfer->isDirty('status') && $transfer->status === 'completed') {
                $transfer->createIncomeTransaction();
            }
        });
    }

    /**
     * Calcule la commission (10% fixe sur le prix).
     */
    public function calculateCommission(): void
    {
        $rate = Setting::get('commission_rate_transfer', self::COMMISSION_RATE);
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
     * Créer une transaction d'entrée automatique quand le transfert est terminé.
     */
    public function createIncomeTransaction(): void
    {
        // Vérifier qu'une transaction n'existe pas déjà pour ce transfert
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
            'description' => 'Transfert ' . $this->departure . ' → ' . $this->destination . ' - ' . $this->client_name . ' (' . $this->reference . ')',
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

    /**
     * Obtenir le libellé du statut.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default => $this->status,
        };
    }

    /**
     * Obtenir la couleur du statut.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
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

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUnpaidCommission($query)
    {
        return $query->whereIn('status', ['confirmed', 'completed'])
            ->where('commission_paid', false);
    }
}
