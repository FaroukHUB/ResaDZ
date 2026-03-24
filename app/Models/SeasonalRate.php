<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SeasonalRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'name',
        'start_date',
        'end_date',
        'supplement_amount',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'supplement_amount' => 'integer',
        'is_active' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Périodes qui chevauchent les dates données.
     */
    public function scopeOverlapping($query, string $startDate, string $endDate)
    {
        return $query->where('start_date', '<=', $endDate)
                     ->where('end_date', '>=', $startDate);
    }

    /**
     * Compte le nombre de jours de cette période qui chevauchent l'intervalle donné.
     */
    public function overlappingDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $overlapStart = $this->start_date->greaterThan($start) ? $this->start_date : $start;
        $overlapEnd = $this->end_date->lessThan($end) ? $this->end_date : $end;

        if ($overlapStart->greaterThan($overlapEnd)) {
            return 0;
        }

        return $overlapStart->diffInDays($overlapEnd) + 1;
    }
}
