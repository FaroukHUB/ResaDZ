<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'start_date',
        'end_date',
        'min_days',
        'max_days',
        'modifier_type',
        'modifier_value',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'modifier_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rule) {
            if (empty($rule->slug)) {
                $rule->slug = Str::slug($rule->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function scopeApplicableForDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->where('type', '!=', 'seasonal')
              ->orWhere(function ($sq) use ($startDate, $endDate) {
                  $sq->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeApplicableForDuration($query, $days)
    {
        return $query->where(function ($q) use ($days) {
            $q->where('type', '!=', 'duration')
              ->orWhere(function ($sq) use ($days) {
                  $sq->where(function ($ssq) use ($days) {
                      $ssq->whereNull('min_days')
                          ->orWhere('min_days', '<=', $days);
                  })->where(function ($ssq) use ($days) {
                      $ssq->whereNull('max_days')
                          ->orWhere('max_days', '>=', $days);
                  });
              });
        });
    }

    public function applyToPrice(float $price): float
    {
        if ($this->modifier_type === 'percentage') {
            return $price * (1 + $this->modifier_value / 100);
        }

        return $price + $this->modifier_value;
    }

    public function getFormattedModifierAttribute(): string
    {
        $sign = $this->modifier_value >= 0 ? '+' : '';

        if ($this->modifier_type === 'percentage') {
            return $sign . $this->modifier_value . '%';
        }

        return $sign . number_format($this->modifier_value, 0, ',', ' ') . ' DA';
    }
}
