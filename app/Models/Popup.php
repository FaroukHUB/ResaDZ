<?php

namespace App\Models;

use App\Traits\HasWebpImages;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    use HasWebpImages;

    public function getWebpImageFields(): array
    {
        return ['image'];
    }

    protected $fillable = [
        'name',
        'title',
        'content',
        'image',
        'button_text',
        'button_url',
        'button_color',
        'size',
        'position',
        'background_color',
        'text_color',
        'show_overlay',
        'closable',
        'is_active',
        'starts_at',
        'ends_at',
        'show_on_pages',
        'show_on_mobile',
        'show_on_desktop',
        'frequency',
        'delay_seconds',
        'priority',
        'views_count',
        'clicks_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_overlay' => 'boolean',
        'closable' => 'boolean',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'show_on_pages' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get active popups that should be displayed
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Order by priority (highest first)
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('priority');
    }

    /**
     * Check if popup should be shown on a specific page type
     */
    public function shouldShowOnPage(?string $pageType): bool
    {
        if (empty($this->show_on_pages)) {
            return true; // Show on all pages if not specified
        }

        return in_array($pageType, $this->show_on_pages);
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment click count
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    /**
     * Get click-through rate
     */
    public function getCtrAttribute(): float
    {
        if ($this->views_count === 0) {
            return 0;
        }

        return round(($this->clicks_count / $this->views_count) * 100, 2);
    }

    /**
     * Get size classes for frontend
     */
    public function getSizeClassesAttribute(): string
    {
        return match ($this->size) {
            'small' => 'max-w-sm',
            'large' => 'max-w-2xl',
            default => 'max-w-lg',
        };
    }

    /**
     * Get position classes for frontend
     */
    public function getPositionClassesAttribute(): string
    {
        return match ($this->position) {
            'bottom-right' => 'items-end justify-end p-4',
            'bottom-left' => 'items-end justify-start p-4',
            default => 'items-center justify-center',
        };
    }
}
