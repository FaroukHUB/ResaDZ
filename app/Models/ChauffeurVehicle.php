<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChauffeurVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'loueur_id',
        'brand',
        'model',
        'year',
        'license_plate',
        'color',
        'vehicle_type',
        'seats',
        'luggage_capacity',
        'hand_luggage_capacity',
        'has_air_conditioning',
        'has_wifi',
        'has_usb_charger',
        'has_child_seat',
        'has_wheelchair_access',
        'accepts_animals',
        'photos',
        'is_active',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'seats' => 'integer',
        'luggage_capacity' => 'integer',
        'hand_luggage_capacity' => 'integer',
        'has_air_conditioning' => 'boolean',
        'has_wifi' => 'boolean',
        'has_usb_charger' => 'boolean',
        'has_child_seat' => 'boolean',
        'has_wheelchair_access' => 'boolean',
        'accepts_animals' => 'boolean',
        'photos' => 'array',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public const VEHICLE_TYPES = [
        'berline' => 'Berline',
        'suv' => 'SUV',
        'van' => 'Van',
        'minibus' => 'Minibus',
        'luxury' => 'Luxe',
        'moto' => 'Moto',
    ];

    /**
     * Le chauffeur proprietaire du vehicule
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
     * Reservations de transfert avec ce vehicule
     */
    public function transferBookings(): HasMany
    {
        return $this->hasMany(TransferBooking::class, 'chauffeur_vehicle_id');
    }

    /**
     * Livraisons avec ce vehicule
     */
    public function deliveryBookings(): HasMany
    {
        return $this->hasMany(DeliveryBooking::class, 'chauffeur_vehicle_id');
    }

    /**
     * Nom complet du vehicule
     */
    public function getFullNameAttribute(): string
    {
        $name = "{$this->brand} {$this->model}";
        if ($this->year) {
            $name .= " ({$this->year})";
        }
        return $name;
    }

    /**
     * Label du type de vehicule
     */
    public function getVehicleTypeLabelAttribute(): string
    {
        return self::VEHICLE_TYPES[$this->vehicle_type] ?? $this->vehicle_type;
    }

    /**
     * Description des capacites
     */
    public function getCapacityDescriptionAttribute(): string
    {
        return "{$this->seats} places, {$this->luggage_capacity} valises";
    }

    /**
     * Liste des equipements disponibles
     */
    public function getEquipmentsAttribute(): array
    {
        $equipments = [];

        if ($this->has_air_conditioning) $equipments[] = 'Climatisation';
        if ($this->has_wifi) $equipments[] = 'WiFi';
        if ($this->has_usb_charger) $equipments[] = 'Chargeur USB';
        if ($this->has_child_seat) $equipments[] = 'Siege enfant';
        if ($this->has_wheelchair_access) $equipments[] = 'Acces fauteuil roulant';
        if ($this->accepts_animals) $equipments[] = 'Animaux acceptes';

        return $equipments;
    }

    /**
     * Premiere photo ou placeholder
     */
    public function getMainPhotoAttribute(): ?string
    {
        if (!$this->photos || empty($this->photos)) {
            return null;
        }
        return $this->photos[0] ?? null;
    }

    /**
     * Scope vehicules actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Definir comme vehicule principal (et retirer le statut aux autres)
     */
    public function setAsPrimary(): void
    {
        // Retirer le statut principal des autres vehicules
        static::where('loueur_id', $this->loueur_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }
}
