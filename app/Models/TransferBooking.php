<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TransferBooking extends Model
{
    protected $fillable = [
        'reference',
        'loueur_id',
        'departure',
        'destination',
        'transfer_date',
        'transfer_time',
        'passengers',
        'luggage_count',
        'price',
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
    ];

    protected static function booted(): void
    {
        static::creating(function ($transfer) {
            if (empty($transfer->reference)) {
                $transfer->reference = 'TRF-' . strtoupper(Str::random(8));
            }
        });
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }
}
