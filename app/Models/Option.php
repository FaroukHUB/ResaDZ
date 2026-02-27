<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'price_eur',
        'price_type',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_eur' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($option) {
            if (empty($option->slug)) {
                $option->slug = Str::slug($option->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price_type === 'free') {
            return 'Gratuit';
        }

        $suffix = $this->price_type === 'per_day' ? '/jour' : '';
        return number_format($this->price, 0, ',', ' ') . ' DA' . $suffix;
    }

    public function getFormattedPriceEurAttribute(): string
    {
        if ($this->price_type === 'free' || !$this->price_eur) {
            return '';
        }

        $suffix = $this->price_type === 'per_day' ? '/jour' : '';
        return number_format($this->price_eur, 0, ',', ' ') . ' €' . $suffix;
    }

    public function getPrice(string $currency = 'DZD'): ?float
    {
        if ($this->price_type === 'free') {
            return 0;
        }

        return $currency === 'EUR' ? $this->price_eur : $this->price;
    }
}
