<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'loueur_id',
        'name',
        'slug',
        'icon',
        'color',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Catégories par défaut
    public static function getDefaultCategories(): array
    {
        return [
            ['name' => 'Carburant', 'slug' => 'carburant', 'icon' => 'fuel', 'color' => '#f59e0b'],
            ['name' => 'Maintenance', 'slug' => 'maintenance', 'icon' => 'wrench', 'color' => '#6366f1'],
            ['name' => 'Lavage', 'slug' => 'lavage', 'icon' => 'droplet', 'color' => '#3b82f6'],
            ['name' => 'Assurance', 'slug' => 'assurance', 'icon' => 'shield', 'color' => '#10b981'],
            ['name' => 'Yassir/VTC', 'slug' => 'vtc', 'icon' => 'car', 'color' => '#8b5cf6'],
            ['name' => 'Parking', 'slug' => 'parking', 'icon' => 'parking', 'color' => '#64748b'],
            ['name' => 'Péage', 'slug' => 'peage', 'icon' => 'road', 'color' => '#78716c'],
            ['name' => 'Autre', 'slug' => 'autre', 'icon' => 'ellipsis', 'color' => '#94a3b8'],
        ];
    }
}
