<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\DeliveryZone;
use Carbon\Carbon;

class PricingService
{
    /**
     * Get commission per day from settings
     */
    private function getCommissionPerDay(): int
    {
        return (int) Setting::get('commission_per_day_dzd', 250);
    }

    /**
     * Get weekend days from settings
     */
    private function getWeekendDays(): array
    {
        return Setting::get('weekend_days', ['friday', 'saturday']) ?? ['friday', 'saturday'];
    }

    /**
     * Calcule le prix total d'une location en fonction des règles du loueur.
     * Inclut la commission ResaDZ (configurable) pour les paiements en DZD.
     */
    public function calculate(
        Vehicle $vehicle,
        string $startDate,
        string $endDate,
        ?int $pickupZoneId = null,
        ?int $returnZoneId = null,
        array $selectedOptions = [],
        string $currency = 'DZD'
    ): array {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $totalDays = max(1, $start->diffInDays($end));

        // 1. Prix de base du loueur (ce qu'il reçoit)
        $loueurDailyRate = $currency === 'EUR'
            ? ($vehicle->price_per_day_eur ?? 0)
            : ($vehicle->price_per_day ?? 0);

        // 2. Appliquer le prix dégressif si configuré
        $degressivePricing = $vehicle->degressive_pricing ?? [];
        if (!empty($degressivePricing)) {
            // Trier par from_days descendant pour prendre le meilleur palier applicable
            $applicableTier = collect($degressivePricing)
                ->filter(fn($tier) => isset($tier['from_days']) && $totalDays >= (int)$tier['from_days'])
                ->sortByDesc('from_days')
                ->first();

            if ($applicableTier) {
                $loueurDailyRate = $currency === 'EUR'
                    ? (float)($applicableTier['price_per_day_eur'] ?? $loueurDailyRate)
                    : (float)($applicableTier['price_per_day'] ?? $loueurDailyRate);
            }
        }

        // 3. Commission ResaDZ (uniquement en DZD) - from settings
        $commissionPerDay = $currency === 'EUR' ? 0 : $this->getCommissionPerDay();
        $commissionTotal = $commissionPerDay * $totalDays;

        // Prix de base pour le client = prix loueur + commission
        $clientDailyRate = $loueurDailyRate + $commissionPerDay;
        $basePrice = $clientDailyRate * $totalDays;
        $loueurBasePrice = $loueurDailyRate * $totalDays;

        // NOTE: Les remises par durée sont remplacées par le prix dégressif
        // On garde la structure pour la compatibilité mais on la désactive
        $durationDiscount = 0;
        $durationDiscountPercent = 0;

        // Config du véhicule pour surcharges
        $pricing = $vehicle->pricing ?? [];

        // 4. Surcharge saison (configurée par le loueur dans pricing.by_season)
        $seasonSurcharge = 0;
        $seasonName = null;
        $seasonRules = $pricing['by_season'] ?? [];

        foreach ($seasonRules as $season) {
            if ($this->isInSeason($start, $end, $season['start'] ?? '', $season['end'] ?? '')) {
                $seasonPercent = $season['percent'] ?? 0;
                $seasonSurcharge = round($basePrice * $seasonPercent / 100);
                $seasonName = $season['name'] ?? 'Haute saison';
                break;
            }
        }

        // 5. Surcharge weekend (configurée par le loueur)
        $weekendSurcharge = 0;
        $weekendPercent = $pricing['weekend_surcharge_percent'] ?? 0;
        if ($weekendPercent > 0) {
            $weekendDays = $this->countWeekendDays($start, $end);
            $weekendSurcharge = round($clientDailyRate * $weekendDays * $weekendPercent / 100);
        }

        // 6. Frais de livraison (configurés par zone)
        $deliveryFee = 0;
        if ($pickupZoneId) {
            $zone = DeliveryZone::find($pickupZoneId);
            if ($zone) {
                $deliveryFee = $zone->delivery_fee ?? 0;
            }
        }

        // 7. Frais de retour
        $returnFee = 0;
        if ($returnZoneId) {
            $zone = DeliveryZone::find($returnZoneId);
            if ($zone) {
                $returnFee = $zone->return_fee ?? 0;
            }
        }

        // 8. Options sélectionnées (configurées dans vehicle.available_options)
        $optionsTotal = 0;
        $optionsDetail = [];
        $availableOptions = $vehicle->available_options ?? [];

        foreach ($selectedOptions as $optionName) {
            foreach ($availableOptions as $opt) {
                if (($opt['name'] ?? '') === $optionName) {
                    $optPrice = $opt['price'] ?? 0;
                    $per = $opt['per'] ?? 'day';
                    $optTotal = $per === 'day' ? $optPrice * $totalDays : $optPrice;

                    $optionsTotal += $optTotal;
                    $optionsDetail[] = [
                        'name' => $optionName,
                        'unit_price' => $optPrice,
                        'per' => $per,
                        'total' => $optTotal,
                    ];
                    break;
                }
            }
        }

        // 8. Calcul final (pour le client)
        $subtotal = $basePrice - $durationDiscount + $seasonSurcharge + $weekendSurcharge;
        $total = $subtotal + $deliveryFee + $returnFee + $optionsTotal;

        // Calcul pour le loueur (sans commission)
        $loueurSubtotal = $loueurBasePrice - $durationDiscount + $seasonSurcharge + $weekendSurcharge;
        $loueurTotal = $loueurSubtotal + $deliveryFee + $returnFee + $optionsTotal;

        // 9. Acompte (configuré par le loueur dans ses settings)
        $loueur = $vehicle->loueur;
        $advancePercentage = $loueur ? $loueur->getSetting('advance_percentage', 0) : 0;
        $minAdvance = $loueur ? $loueur->getSetting('min_advance_amount', 0) : 0;
        $advanceAmount = max($minAdvance, round($total * $advancePercentage / 100));

        // 10. Caution (configurée par véhicule)
        $depositAmount = $vehicle->deposit_amount ?? 0;
        $depositCurrency = $vehicle->deposit_currency ?? $currency;

        $currencySymbol = $currency === 'EUR' ? '€' : 'DA';

        return [
            'currency' => $currency,
            'currency_symbol' => $currencySymbol,
            'total_days' => $totalDays,

            // Prix pour le client (incluant commission ResaDZ)
            'daily_rate' => $clientDailyRate,
            'base_price' => $basePrice,
            'subtotal' => $subtotal,
            'total' => $total,

            // Prix pour le loueur (sans commission)
            'loueur_daily_rate' => $loueurDailyRate,
            'loueur_base_price' => $loueurBasePrice,
            'loueur_subtotal' => $loueurSubtotal,
            'loueur_total' => $loueurTotal,

            // Commission ResaDZ
            'commission_per_day' => $commissionPerDay,
            'commission_total' => $commissionTotal,

            'duration_discount' => $durationDiscount,
            'duration_discount_percent' => $durationDiscountPercent,
            'season_surcharge' => $seasonSurcharge,
            'season_name' => $seasonName,
            'weekend_surcharge' => $weekendSurcharge,
            'delivery_fee' => $deliveryFee,
            'return_fee' => $returnFee,
            'options_total' => $optionsTotal,
            'options_detail' => $optionsDetail,
            'advance_percentage' => $advancePercentage,
            'advance_amount' => $advanceAmount,
            'deposit_amount' => $depositAmount,
            'deposit_currency' => $depositCurrency,
            'formatted_total' => number_format($total, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_loueur_total' => number_format($loueurTotal, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_commission' => number_format($commissionTotal, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_advance' => number_format($advanceAmount, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_deposit' => number_format($depositAmount, 0, ',', ' ') . ' ' . ($depositCurrency === 'EUR' ? '€' : 'DA'),
        ];
    }

    /**
     * Vérifie si la période de location tombe dans une saison.
     */
    private function isInSeason(Carbon $start, Carbon $end, string $seasonStart, string $seasonEnd): bool
    {
        if (empty($seasonStart) || empty($seasonEnd)) {
            return false;
        }

        $year = $start->year;
        $sStart = Carbon::createFromFormat('m-d', $seasonStart)->year($year);
        $sEnd = Carbon::createFromFormat('m-d', $seasonEnd)->year($year);

        return $start->lte($sEnd) && $end->gte($sStart);
    }

    /**
     * Compte les jours de weekend dans la période (configurable via settings).
     */
    private function countWeekendDays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();
        $weekendDays = $this->getWeekendDays();

        $dayMap = [
            'monday' => 'isMonday',
            'tuesday' => 'isTuesday',
            'wednesday' => 'isWednesday',
            'thursday' => 'isThursday',
            'friday' => 'isFriday',
            'saturday' => 'isSaturday',
            'sunday' => 'isSunday',
        ];

        while ($current->lte($end)) {
            foreach ($weekendDays as $day) {
                if (isset($dayMap[$day]) && $current->{$dayMap[$day]}()) {
                    $count++;
                    break;
                }
            }
            $current->addDay();
        }

        return $count;
    }
}
