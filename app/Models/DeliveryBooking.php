<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DeliveryBooking extends Model
{
    protected $fillable = [
        'reference',
        'loueur_id',
        'pickup_address',
        'pickup_city',
        'delivery_address',
        'delivery_city',
        'package_type',
        'package_description',
        'weight',
        'price',
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
        });
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
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
}
