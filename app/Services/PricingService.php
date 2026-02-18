<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\DeliveryZone;
use Carbon\Carbon;

class PricingService
{
    /**
     * Calcule le prix total d'une location en fonction des règles du loueur.
     * Aucune valeur hardcodée - tout vient du JSON pricing du véhicule
     * et des settings du loueur.
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

        $pricing = $vehicle->pricing ?? [];

        // 1. Prix de base
        $baseKey = $currency === 'EUR' ? 'base_eur' : 'base';
        $dailyRate = $pricing[$baseKey]['amount']
            ?? ($currency === 'EUR' ? $vehicle->price_per_day_eur : $vehicle->price_per_day)
            ?? 0;

        $basePrice = $dailyRate * $totalDays;

        // 2. Remise durée (configurée par le loueur dans pricing.by_duration)
        $durationDiscount = 0;
        $durationDiscountPercent = 0;
        $durationRules = $pricing['by_duration'] ?? [];

        foreach ($durationRules as $rule) {
            $minDays = $rule['min_days'] ?? 0;
            $maxDays = $rule['max_days'] ?? PHP_INT_MAX;

            if ($totalDays >= $minDays && $totalDays <= ($maxDays ?? PHP_INT_MAX)) {
                $durationDiscountPercent = $rule['discount_percent'] ?? 0;
                $durationDiscount = round($basePrice * $durationDiscountPercent / 100);
                break;
            }
        }

        // 3. Surcharge saison (configurée par le loueur dans pricing.by_season)
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

        // 4. Surcharge weekend (configurée par le loueur)
        $weekendSurcharge = 0;
        $weekendPercent = $pricing['weekend_surcharge_percent'] ?? 0;
        if ($weekendPercent > 0) {
            $weekendDays = $this->countWeekendDays($start, $end);
            $weekendSurcharge = round($dailyRate * $weekendDays * $weekendPercent / 100);
        }

        // 5. Frais de livraison (configurés par zone)
        $deliveryFee = 0;
        if ($pickupZoneId) {
            $zone = DeliveryZone::find($pickupZoneId);
            if ($zone) {
                $deliveryFee = $zone->delivery_fee ?? 0;
            }
        }

        // 6. Frais de retour
        $returnFee = 0;
        if ($returnZoneId) {
            $zone = DeliveryZone::find($returnZoneId);
            if ($zone) {
                $returnFee = $zone->return_fee ?? 0;
            }
        }

        // 7. Options sélectionnées (configurées dans vehicle.available_options)
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

        // 8. Calcul final
        $subtotal = $basePrice - $durationDiscount + $seasonSurcharge + $weekendSurcharge;
        $total = $subtotal + $deliveryFee + $returnFee + $optionsTotal;

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
            'daily_rate' => $dailyRate,
            'base_price' => $basePrice,
            'duration_discount' => $durationDiscount,
            'duration_discount_percent' => $durationDiscountPercent,
            'season_surcharge' => $seasonSurcharge,
            'season_name' => $seasonName,
            'weekend_surcharge' => $weekendSurcharge,
            'delivery_fee' => $deliveryFee,
            'return_fee' => $returnFee,
            'options_total' => $optionsTotal,
            'options_detail' => $optionsDetail,
            'subtotal' => $subtotal,
            'total' => $total,
            'advance_percentage' => $advancePercentage,
            'advance_amount' => $advanceAmount,
            'deposit_amount' => $depositAmount,
            'deposit_currency' => $depositCurrency,
            'formatted_total' => number_format($total, 0, ',', ' ') . ' ' . $currencySymbol,
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
     * Compte les jours de weekend dans la période.
     */
    private function countWeekendDays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // Vendredi et samedi = weekend en Algérie
            if ($current->isFriday() || $current->isSaturday()) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }
}
