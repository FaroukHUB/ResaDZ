<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\DeliveryZone;
use Carbon\Carbon;

class PricingService
{
    /**
     * Get commission rate based on rental duration (degressive rates).
     * - 1 to 10 days: 8%
     * - More than 10 days: 6%
     *
     * Commission is only charged to loueur, client pays no service fee.
     */
    private function getCommissionRate(int $totalDays): float
    {
        $rate1to10 = (float) Setting::get('commission_rate_1_to_10_days', 8);
        $rate11plus = (float) Setting::get('commission_rate_11_plus_days', 6);

        if ($totalDays > 10) {
            return $rate11plus;
        }
        return $rate1to10;
    }

    /**
     * Get commission tier label for display
     */
    public function getCommissionTierLabel(int $totalDays): string
    {
        if ($totalDays > 10) {
            return '+ de 10 jours';
        }
        return '1-10 jours';
    }

    /**
     * Get DZD to EUR conversion rate from settings
     */
    private function getDzdToEurRate(): float
    {
        return (float) Setting::get('dzd_to_eur_rate', 0.0068);
    }

    /**
     * Convert DZD amount to EUR
     */
    public function convertToEur(float $amountDzd): float
    {
        return round($amountDzd * $this->getDzdToEurRate(), 2);
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
        $degressiveApplied = false;
        $degressiveFromDays = null;
        $originalDailyRate = $loueurDailyRate;

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
                $degressiveApplied = true;
                $degressiveFromDays = (int)($applicableTier['from_days'] ?? 0);
            }
        }

        // 3. Prix de base (nouveau modèle 2026 - pas de frais de service client)
        // Le locataire ne paye AUCUNE commission, il paie le prix affiché par le loueur
        $basePrice = $loueurDailyRate * $totalDays;
        $clientDailyRate = $loueurDailyRate; // Client paie le même prix que le loueur affiche
        $loueurBasePrice = $basePrice;

        // Client service fee = 0 (nouveau modèle 2026)
        $clientServiceFeePerDay = 0;
        $clientServiceFeeTotal = 0;

        // NOTE: Les remises par durée sont remplacées par le prix dégressif
        // On garde la structure pour la compatibilité mais on la désactive
        $durationDiscount = 0;
        $durationDiscountPercent = 0;

        // Récupérer le taux de commission dégressif selon la durée
        // Taux: 1-10j → 8%, +10j → 6%
        $commissionRate = $this->getCommissionRate($totalDays);
        $commissionTier = $this->getCommissionTierLabel($totalDays);

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

        // 9. Commission ResaDZ (nouveau modèle 2026)
        // Commission calculée sur le SUBTOTAL (montant HT de la location, sans livraison/options)
        // Les frais de livraison et options ne sont pas commissionnés
        $loueurCommissionTotal = round($subtotal * $commissionRate / 100, 2);
        $loueurCommissionPerDay = round($loueurCommissionTotal / $totalDays, 2);

        // Calcul pour le loueur (ce qu'il reçoit après commission ResaDZ)
        $loueurNetBasePrice = $subtotal - $loueurCommissionTotal;
        $loueurNetDailyRate = round($loueurNetBasePrice / $totalDays, 2);
        $loueurSubtotal = $loueurNetBasePrice;
        // Le loueur garde 100% des frais de livraison, retour et options (pas commissionnés)
        $loueurTotal = $loueurSubtotal + $deliveryFee + $returnFee + $optionsTotal;

        // 10. Acompte (configuré par le loueur dans ses settings)
        // Calculé en pourcentage du total, dans la devise de la réservation (DZD ou EUR)
        $loueur = $vehicle->loueur;
        $advancePercentage = $loueur ? $loueur->getSetting('advance_percentage', 0) : 0;
        $advanceAmount = round($total * $advancePercentage / 100);

        // 11. Caution (configurée par véhicule - montants DA et EUR définis par le loueur)
        $depositAmountDa = $vehicle->deposit_amount ?? 0;
        $depositAmountEur = $vehicle->deposit_amount_eur ?? 0;

        // Pour la compatibilité, on garde deposit_amount et deposit_currency basés sur la devise de réservation
        $depositAmount = $currency === 'EUR' ? $depositAmountEur : $depositAmountDa;
        $depositCurrency = $currency;

        $currencySymbol = $currency === 'EUR' ? '€' : 'DA';

        // Calcul du total en EUR (si le véhicule a un prix EUR)
        // On calcule le total EUR en utilisant price_per_day_eur si disponible
        $pricePerDayEur = $vehicle->price_per_day_eur ?? 0;
        $totalEur = 0;
        $advanceAmountEur = 0;

        if ($pricePerDayEur > 0) {
            // Calculer le total EUR de la même manière que le total DA
            $totalEur = $pricePerDayEur * $totalDays;
            // Appliquer le prix dégressif si applicable
            if ($degressiveApplied && !empty($vehicle->degressive_pricing)) {
                $applicableTier = collect($vehicle->degressive_pricing)
                    ->filter(fn($tier) => isset($tier['from_days']) && $totalDays >= (int)$tier['from_days'])
                    ->sortByDesc('from_days')
                    ->first();
                if ($applicableTier && isset($applicableTier['price_per_day_eur'])) {
                    $totalEur = (float)$applicableTier['price_per_day_eur'] * $totalDays;
                }
            }
            // Ajouter les frais (delivery, return, options) - pour simplifier, on les garde en DA
            // Note: les frais de livraison/retour/options sont en DA, pas convertis
            $advanceAmountEur = round($totalEur * $advancePercentage / 100);
        }

        return [
            'currency' => $currency,
            'currency_symbol' => $currencySymbol,
            'total_days' => $totalDays,

            // Prix pour le client (incluant commission ResaDZ)
            'daily_rate' => $clientDailyRate,
            'base_price' => $basePrice,
            'subtotal' => $subtotal,
            'total' => $total,

            // Prix affiché par le loueur (avant commission)
            'loueur_daily_rate' => $loueurDailyRate,
            'loueur_base_price' => $loueurBasePrice,

            // Ce que le loueur reçoit réellement (après commission ResaDZ)
            'loueur_net_daily_rate' => $loueurNetDailyRate,
            'loueur_net_base_price' => $loueurNetBasePrice,
            'loueur_subtotal' => $loueurSubtotal,
            'loueur_total' => $loueurTotal,

            // Commission ResaDZ (prélevée au loueur uniquement - nouveau modèle 2026)
            'loueur_commission_per_day' => $loueurCommissionPerDay,
            'loueur_commission_total' => $loueurCommissionTotal,
            'commission_rate' => $commissionRate,
            'commission_tier' => $commissionTier,

            // Frais de service client = 0 (nouveau modèle 2026 - locataire ne paie rien)
            'client_service_fee_per_day' => 0,
            'client_service_fee_total' => 0,

            'duration_discount' => $durationDiscount,
            'duration_discount_percent' => $durationDiscountPercent,
            'degressive_applied' => $degressiveApplied,
            'degressive_from_days' => $degressiveFromDays,
            'season_surcharge' => $seasonSurcharge,
            'season_name' => $seasonName,
            'weekend_surcharge' => $weekendSurcharge,
            'delivery_fee' => $deliveryFee,
            'return_fee' => $returnFee,
            'options_total' => $optionsTotal,
            'options_detail' => $optionsDetail,
            'advance_percentage' => $advancePercentage,
            'advance_amount' => $advanceAmount,
            'advance_amount_eur' => $advanceAmountEur,
            'deposit_amount' => $depositAmount,
            'deposit_amount_da' => $depositAmountDa,
            'deposit_amount_eur' => $depositAmountEur,
            'deposit_currency' => $depositCurrency,
            'formatted_total' => number_format($total, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_loueur_total' => number_format($loueurTotal, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_loueur_commission' => number_format($loueurCommissionTotal, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_client_service_fee' => number_format($clientServiceFeeTotal, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_advance' => number_format($advanceAmount, 0, ',', ' ') . ' ' . $currencySymbol,
            'formatted_deposit' => number_format($depositAmount, 0, ',', ' ') . ' ' . ($depositCurrency === 'EUR' ? '€' : 'DA'),

            // Both currency amounts (defined by loueur, not converted)
            'total_eur' => $totalEur,
            'formatted_total_eur' => $totalEur > 0 ? number_format($totalEur, 0, ',', ' ') . ' €' : '',
            'formatted_advance_eur' => $advanceAmountEur > 0 ? number_format($advanceAmountEur, 0, ',', ' ') . ' €' : '',
            'formatted_deposit_da' => $depositAmountDa > 0 ? number_format($depositAmountDa, 0, ',', ' ') . ' DA' : '',
            'formatted_deposit_eur' => $depositAmountEur > 0 ? number_format($depositAmountEur, 0, ',', ' ') . ' €' : '',
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
