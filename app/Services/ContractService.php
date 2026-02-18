<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Loueur;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ContractService
{
    /**
     * Generate a rental contract PDF for a booking.
     */
    public function generateContract(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        $booking->load(['vehicle.brand', 'vehicle.category', 'loueur']);

        $data = $this->prepareContractData($booking);

        return Pdf::loadView('pdf.contract', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Prepare all data needed for the contract.
     */
    private function prepareContractData(Booking $booking): array
    {
        $loueur = $booking->loueur;
        $vehicle = $booking->vehicle;

        // Get conditions from loueur settings
        $conditions = $loueur ? $loueur->getConditions() : [];

        // Calculate totals
        $totalDays = $booking->total_days;
        $pricingService = new PricingService();

        return [
            'booking' => $booking,
            'loueur' => $loueur,
            'vehicle' => $vehicle,

            // Contract reference
            'contract_number' => 'CTR-' . $booking->reference,
            'contract_date' => now()->format('d/m/Y'),

            // Client info
            'client_name' => $booking->client_name,
            'client_phone' => $booking->client_phone,
            'client_email' => $booking->client_email,

            // Rental dates
            'start_date' => $booking->start_date->format('d/m/Y'),
            'end_date' => $booking->end_date->format('d/m/Y'),
            'start_time' => '09:00', // Default, can be customized
            'end_time' => '18:00',
            'total_days' => $totalDays,

            // Vehicle info
            'vehicle_name' => $vehicle->full_name ?? '',
            'vehicle_brand' => $vehicle->brand->name ?? '',
            'vehicle_category' => $vehicle->category->name ?? '',
            'vehicle_year' => $vehicle->year ?? '',
            'vehicle_transmission' => $this->translateTransmission($vehicle->transmission),
            'vehicle_fuel' => $this->translateFuel($vehicle->fuel_type),
            'vehicle_color' => $vehicle->color ?? '',
            'vehicle_seats' => $vehicle->seats ?? '',
            'vehicle_mileage' => $vehicle->mileage ? number_format($vehicle->mileage, 0, ',', ' ') . ' km' : 'N/A',

            // Pricing
            'price_per_day' => number_format($booking->base_price / $totalDays, 0, ',', ' '),
            'total_rental' => number_format($booking->base_price, 0, ',', ' '),
            'duration_discount' => number_format($booking->duration_discount ?? 0, 0, ',', ' '),
            'delivery_fee' => number_format($booking->delivery_fee ?? 0, 0, ',', ' '),
            'return_fee' => number_format($booking->return_fee ?? 0, 0, ',', ' '),
            'options_total' => number_format($booking->options_total ?? 0, 0, ',', ' '),
            'total_price' => number_format($booking->total_price, 0, ',', ' '),
            'currency' => $booking->currency ?? 'DZD',
            'currency_symbol' => $booking->currency === 'EUR' ? '€' : 'DA',

            // Deposit
            'deposit_amount' => number_format($booking->deposit_amount ?? 0, 0, ',', ' '),
            'deposit_currency' => $booking->deposit_currency === 'EUR' ? '€' : 'DA',

            // Advance payment
            'advance_amount' => number_format($booking->advance_amount ?? 0, 0, ',', ' '),
            'remaining_amount' => number_format($booking->amount_remaining ?? $booking->total_price, 0, ',', ' '),

            // Loueur info
            'loueur_name' => $loueur->company_name ?? 'ResaDZ',
            'loueur_phone' => $loueur->phone ?? '',
            'loueur_email' => $loueur->email_contact ?? '',
            'loueur_address' => $loueur->address ?? '',
            'loueur_city' => $loueur->city ?? '',
            'loueur_wilaya' => $loueur->wilaya ?? '',

            // Pickup/Return locations
            'pickup_address' => $booking->pickup_address ?? $loueur->address ?? '',
            'return_address' => $booking->return_address ?? $loueur->address ?? '',

            // Conditions
            'conditions' => $conditions,

            // Selected options
            'selected_options' => $booking->selected_options ?? [],
        ];
    }

    /**
     * Translate transmission type to French.
     */
    private function translateTransmission(?string $transmission): string
    {
        return match($transmission) {
            'manual' => 'Manuelle',
            'automatic' => 'Automatique',
            default => $transmission ?? 'N/A',
        };
    }

    /**
     * Translate fuel type to French.
     */
    private function translateFuel(?string $fuel): string
    {
        return match($fuel) {
            'essence' => 'Essence',
            'diesel' => 'Diesel',
            'hybrid' => 'Hybride',
            'electric' => 'Électrique',
            default => $fuel ?? 'N/A',
        };
    }

    /**
     * Get the filename for a contract PDF.
     */
    public function getFilename(Booking $booking): string
    {
        return 'contrat-' . $booking->reference . '.pdf';
    }
}
