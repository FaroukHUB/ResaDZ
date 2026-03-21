<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChauffeurOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'loueur_id',
        'name',
        'description',
        'icon',
        'pricing_type',
        'price',
        'category',
        'is_active',
        'max_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'max_quantity' => 'integer',
    ];

    public const PRICING_TYPES = [
        'free' => 'Gratuit',
        'paid' => 'Payant',
        'on_request' => 'Sur demande',
    ];

    public const CATEGORIES = [
        'comfort' => 'Confort',
        'child' => 'Enfants',
        'accessibility' => 'Accessibilite',
        'luggage' => 'Bagages',
        'service' => 'Services',
        'other' => 'Autres',
    ];

    public const DEFAULT_OPTIONS = [
        ['name' => 'WiFi gratuit', 'category' => 'comfort', 'pricing_type' => 'free', 'icon' => 'heroicon-o-wifi'],
        ['name' => 'Bouteille d\'eau', 'category' => 'comfort', 'pricing_type' => 'free', 'icon' => 'heroicon-o-beaker'],
        ['name' => 'Journaux/Magazines', 'category' => 'comfort', 'pricing_type' => 'free', 'icon' => 'heroicon-o-newspaper'],
        ['name' => 'Chargeur telephone', 'category' => 'comfort', 'pricing_type' => 'free', 'icon' => 'heroicon-o-bolt'],
        ['name' => 'Siege auto bebe', 'category' => 'child', 'pricing_type' => 'free', 'icon' => 'heroicon-o-user'],
        ['name' => 'Rehausseur enfant', 'category' => 'child', 'pricing_type' => 'free', 'icon' => 'heroicon-o-user-plus'],
        ['name' => 'Accueil aeroport (panneau)', 'category' => 'service', 'pricing_type' => 'paid', 'price' => 500, 'icon' => 'heroicon-o-identification'],
        ['name' => 'Attente supplementaire (30 min)', 'category' => 'service', 'pricing_type' => 'paid', 'price' => 1000, 'icon' => 'heroicon-o-clock'],
        ['name' => 'Bagage supplementaire', 'category' => 'luggage', 'pricing_type' => 'paid', 'price' => 300, 'icon' => 'heroicon-o-archive-box'],
    ];

    /**
     * Le chauffeur proprietaire de l'option
     */
    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    /**
     * Alias pour chauffeur
     */
    public function chauffeur(): BelongsTo
    {
        return $this->loueur();
    }

    /**
     * Reservations de transfert utilisant cette option
     */
    public function transferBookings(): BelongsToMany
    {
        return $this->belongsToMany(TransferBooking::class, 'transfer_booking_options')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    /**
     * Livraisons utilisant cette option
     */
    public function deliveryBookings(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryBooking::class, 'delivery_booking_options')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    /**
     * Label du type de tarification
     */
    public function getPricingTypeLabelAttribute(): string
    {
        return self::PRICING_TYPES[$this->pricing_type] ?? $this->pricing_type;
    }

    /**
     * Label de la categorie
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Prix formate
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->pricing_type === 'free') {
            return 'Gratuit';
        }
        if ($this->pricing_type === 'on_request') {
            return 'Sur demande';
        }
        return number_format($this->price, 0, ',', ' ') . ' DA';
    }

    /**
     * Scope options actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope par categorie
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope options gratuites
     */
    public function scopeFree($query)
    {
        return $query->where('pricing_type', 'free');
    }

    /**
     * Scope options payantes
     */
    public function scopePaid($query)
    {
        return $query->where('pricing_type', 'paid');
    }

    /**
     * Creer les options par defaut pour un chauffeur
     */
    public static function createDefaultsForChauffeur(int $loueurId): void
    {
        foreach (self::DEFAULT_OPTIONS as $option) {
            self::create(array_merge($option, [
                'loueur_id' => $loueurId,
                'is_active' => true,
                'max_quantity' => 1,
            ]));
        }
    }
}
