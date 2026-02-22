<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Loueur;
use Carbon\Carbon;

class OptionAvailabilityService
{
    /**
     * Get available options for a loueur during specific dates.
     *
     * @param Loueur $loueur
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeBookingId Exclude a specific booking (for edits)
     * @return array Options with availability info
     */
    public function getAvailableOptions(Loueur $loueur, string $startDate, string $endDate, ?int $excludeBookingId = null): array
    {
        $rentalOptions = $loueur->getSetting('rental_options', []);

        if (empty($rentalOptions)) {
            return [];
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get all bookings that overlap with the requested dates
        $overlappingBookings = Booking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->get();

        // Count how many of each option is used
        $optionUsage = [];
        foreach ($overlappingBookings as $booking) {
            $selectedOptions = $booking->selected_options ?? [];
            foreach ($selectedOptions as $optionName) {
                if (!isset($optionUsage[$optionName])) {
                    $optionUsage[$optionName] = 0;
                }
                $optionUsage[$optionName]++;
            }
        }

        // Check availability for each option
        $result = [];
        foreach ($rentalOptions as $option) {
            $optionName = $option['name'] ?? '';
            $quantity = (int) ($option['quantity'] ?? 1);
            $used = $optionUsage[$optionName] ?? 0;
            $available = $quantity - $used;

            $result[] = [
                'name' => $optionName,
                'price' => (float) ($option['price'] ?? 0),
                'per' => $option['per'] ?? 'day',
                'quantity' => $quantity,
                'used' => $used,
                'available' => max(0, $available),
                'is_available' => $available > 0,
                'is_free' => ($option['is_free'] ?? false) || (($option['price'] ?? 0) == 0),
                'image' => $option['image'] ?? null,
                'description' => $option['description'] ?? null,
            ];
        }

        return $result;
    }

    /**
     * Check if a specific option is available.
     *
     * @param Loueur $loueur
     * @param string $optionName
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeBookingId
     * @return bool
     */
    public function isOptionAvailable(Loueur $loueur, string $optionName, string $startDate, string $endDate, ?int $excludeBookingId = null): bool
    {
        $options = $this->getAvailableOptions($loueur, $startDate, $endDate, $excludeBookingId);

        foreach ($options as $option) {
            if ($option['name'] === $optionName) {
                return $option['is_available'];
            }
        }

        return false;
    }

    /**
     * Validate that all requested options are available.
     *
     * @param Loueur $loueur
     * @param array $requestedOptions
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeBookingId
     * @return array ['valid' => bool, 'unavailable' => array]
     */
    public function validateOptions(Loueur $loueur, array $requestedOptions, string $startDate, string $endDate, ?int $excludeBookingId = null): array
    {
        $availableOptions = $this->getAvailableOptions($loueur, $startDate, $endDate, $excludeBookingId);
        $unavailable = [];

        foreach ($requestedOptions as $optionName) {
            // Skip return options (not inventory-based)
            if (in_array($optionName, ['Retour sans plein', 'Retour sans lavage'])) {
                continue;
            }

            $found = false;
            foreach ($availableOptions as $option) {
                if ($option['name'] === $optionName) {
                    $found = true;
                    if (!$option['is_available']) {
                        $unavailable[] = $optionName;
                    }
                    break;
                }
            }

            // Option not found in loueur's options = ignore it
        }

        return [
            'valid' => empty($unavailable),
            'unavailable' => $unavailable,
        ];
    }
}
