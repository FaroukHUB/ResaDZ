<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loueur_id',
        'type',
        'booking_id',
        'vehicle_id',
        'expense_category_id',
        'description',
        'notes',
        'amount',
        'currency',
        'amount_eur',
        'payment_method',
        'payment_reference',
        'is_commission',
        'transaction_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_eur' => 'decimal:2',
        'is_commission' => 'boolean',
        'transaction_date' => 'date',
    ];

    // Relations
    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    // Helpers
    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function getFormattedAmount(): string
    {
        $symbol = $this->currency === 'EUR' ? '€' : 'DA';
        $prefix = $this->isExpense() ? '-' : '+';
        return $prefix . number_format($this->amount, 0, ',', ' ') . ' ' . $symbol;
    }

    // Scopes
    public function scopeForLoueur($query, int $loueurId)
    {
        return $query->where('loueur_id', $loueurId);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeForVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
