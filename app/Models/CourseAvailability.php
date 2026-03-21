<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CourseAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'loueur_id',
        'type',
        'for_transfer',
        'for_delivery',
        'date',
        'start_time',
        'end_time',
        'recurrence',
        'recurrence_end',
        'recurrence_days',
        'wilaya',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'recurrence_end' => 'date',
        'recurrence_days' => 'array',
        'for_transfer' => 'boolean',
        'for_delivery' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'available' => 'Disponible',
        'unavailable' => 'Indisponible',
    ];

    public const RECURRENCES = [
        'none' => 'Pas de recurrence',
        'daily' => 'Tous les jours',
        'weekly' => 'Chaque semaine',
        'monthly' => 'Chaque mois',
    ];

    public const DAYS_OF_WEEK = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    /**
     * Le loueur proprietaire de cette disponibilite
     */
    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    /**
     * Verifie si cette disponibilite s'applique a une date donnee
     */
    public function appliesTo(Carbon $date): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Date unique (pas de recurrence)
        if ($this->recurrence === 'none') {
            return $this->date->isSameDay($date);
        }

        // La date doit etre apres ou egale a la date de debut
        if ($date->lt($this->date)) {
            return false;
        }

        // La date doit etre avant la fin de recurrence si definie
        if ($this->recurrence_end && $date->gt($this->recurrence_end)) {
            return false;
        }

        switch ($this->recurrence) {
            case 'daily':
                return true;

            case 'weekly':
                if ($this->recurrence_days && is_array($this->recurrence_days)) {
                    return in_array($date->dayOfWeekIso, $this->recurrence_days);
                }
                return $date->dayOfWeekIso === $this->date->dayOfWeekIso;

            case 'monthly':
                return $date->day === $this->date->day;

            default:
                return false;
        }
    }

    /**
     * Description du creneau horaire
     */
    public function getTimeSlotDescriptionAttribute(): string
    {
        if (!$this->start_time && !$this->end_time) {
            return 'Toute la journee';
        }

        $start = $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : '00:00';
        $end = $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : '23:59';

        return "{$start} - {$end}";
    }

    /**
     * Description de la recurrence
     */
    public function getRecurrenceDescriptionAttribute(): string
    {
        if ($this->recurrence === 'none') {
            return $this->date->format('d/m/Y');
        }

        $desc = self::RECURRENCES[$this->recurrence] ?? $this->recurrence;

        if ($this->recurrence === 'weekly' && $this->recurrence_days) {
            $days = array_map(fn($d) => self::DAYS_OF_WEEK[$d] ?? $d, $this->recurrence_days);
            $desc .= ' (' . implode(', ', $days) . ')';
        }

        if ($this->recurrence_end) {
            $desc .= ' jusqu\'au ' . $this->recurrence_end->format('d/m/Y');
        }

        return $desc;
    }

    /**
     * Description des services
     */
    public function getServicesDescriptionAttribute(): string
    {
        $services = [];
        if ($this->for_transfer) $services[] = 'Transferts';
        if ($this->for_delivery) $services[] = 'Livraisons';

        return implode(', ', $services) ?: 'Aucun service';
    }

    /**
     * Scope disponibilites actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope par type (available/unavailable)
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les transferts
     */
    public function scopeForTransfer($query)
    {
        return $query->where('for_transfer', true);
    }

    /**
     * Scope pour les livraisons
     */
    public function scopeForDelivery($query)
    {
        return $query->where('for_delivery', true);
    }

    /**
     * Verifie si un loueur est disponible a une date/heure donnee pour un service
     */
    public static function isAvailable(int $loueurId, Carbon $datetime, string $service = 'transfer'): bool
    {
        $date = $datetime->copy()->startOfDay();
        $time = $datetime->format('H:i:s');

        $serviceField = $service === 'delivery' ? 'for_delivery' : 'for_transfer';

        // Recuperer toutes les disponibilites actives du loueur
        $availabilities = self::where('loueur_id', $loueurId)
            ->where('is_active', true)
            ->where($serviceField, true)
            ->get();

        $isAvailable = false;
        $isUnavailable = false;

        foreach ($availabilities as $availability) {
            if (!$availability->appliesTo($date)) {
                continue;
            }

            // Verifier l'heure si definie
            $timeMatches = true;
            if ($availability->start_time || $availability->end_time) {
                $start = $availability->start_time ? Carbon::parse($availability->start_time)->format('H:i:s') : '00:00:00';
                $end = $availability->end_time ? Carbon::parse($availability->end_time)->format('H:i:s') : '23:59:59';
                $timeMatches = $time >= $start && $time <= $end;
            }

            if (!$timeMatches) {
                continue;
            }

            if ($availability->type === 'available') {
                $isAvailable = true;
            } else {
                $isUnavailable = true;
            }
        }

        // Indisponible l'emporte sur disponible
        if ($isUnavailable) {
            return false;
        }

        return $isAvailable;
    }

    /**
     * Recuperer les loueurs disponibles pour une date/heure et un service
     */
    public static function getAvailableLoueurs(Carbon $datetime, string $service = 'transfer', ?string $wilaya = null): \Illuminate\Support\Collection
    {
        $date = $datetime->copy()->startOfDay();
        $serviceField = $service === 'delivery' ? 'for_delivery' : 'for_transfer';
        $offersField = $service === 'delivery' ? 'offers_delivery' : 'offers_transfer';

        // Recuperer les loueurs qui proposent ce service
        $loueurs = Loueur::where('is_active', true)
            ->where('is_suspended', false)
            ->where($offersField, true)
            ->when($wilaya, fn($q) => $q->where('wilaya', $wilaya))
            ->get();

        return $loueurs->filter(function ($loueur) use ($datetime, $service) {
            return self::isAvailable($loueur->id, $datetime, $service);
        });
    }
}
