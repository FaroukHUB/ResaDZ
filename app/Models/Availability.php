<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'reservation_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function scopeBlocked($query)
    {
        return $query->where('type', 'blocked');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('type', 'maintenance');
    }

    public function scopeReserved($query)
    {
        return $query->where('type', 'reserved');
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($sq) use ($startDate, $endDate) {
                  $sq->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'blocked' => 'Bloqué',
            'maintenance' => 'Maintenance',
            'reserved' => 'Réservé',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'blocked' => 'danger',
            'maintenance' => 'warning',
            'reserved' => 'info',
            default => 'gray',
        };
    }
}
