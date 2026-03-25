<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Availability;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:500',
            'start_date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'pickup_location' => 'nullable|string|max:255',
            'return_location' => 'nullable|string|max:255',
            'flight_number' => 'nullable|string|max:50',
            'selected_options' => 'nullable|array',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier la disponibilité du véhicule
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        if (!$vehicle->isAvailableForDates($validated['start_date'], $validated['end_date'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce véhicule n\'est pas disponible pour les dates sélectionnées.',
            ], 422);
        }

        // Calculer le prix
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;

        $basePrice = $vehicle->price_per_day * $days;

        // Calculer le prix des options
        $optionsPrice = 0;
        $selectedOptions = $validated['selected_options'] ?? [];
        if (!empty($selectedOptions)) {
            $rentalOptions = $vehicle->getRentalOptions();
            foreach ($rentalOptions as $option) {
                $optionName = $option['name'] ?? '';
                if (in_array($optionName, $selectedOptions)) {
                    $isFree = ($option['is_free'] ?? false) || (($option['price'] ?? 0) == 0);
                    if (!$isFree) {
                        $price = (float) ($option['price'] ?? 0);
                        $per = $option['per'] ?? 'day';
                        $optionsPrice += ($per === 'day') ? $price * $days : $price;
                    }
                }
            }
        }

        $totalPrice = $basePrice + $optionsPrice;

        try {
            DB::beginTransaction();

            // Créer la réservation
            $reservation = Reservation::create([
                'vehicle_id' => $validated['vehicle_id'],
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'],
                'client_email' => $validated['client_email'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_date' => $validated['end_date'],
                'end_time' => $validated['end_time'] ?? null,
                'pickup_location' => $validated['pickup_location'] ?? null,
                'return_location' => $validated['return_location'] ?? null,
                'flight_number' => $validated['flight_number'] ?? null,
                'base_price' => $basePrice,
                'options_price' => $optionsPrice,
                'discount' => 0,
                'total_price' => $totalPrice,
                'currency' => 'DZD',
                'selected_options' => $validated['selected_options'] ?? null,
                'status' => 'pending',
                'client_notes' => $validated['client_notes'] ?? null,
                'source' => 'website',
            ]);

            // Bloquer les disponibilités
            Availability::create([
                'vehicle_id' => $vehicle->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'type' => 'reserved',
                'reason' => 'Réservation #' . $reservation->reference,
                'reservation_id' => $reservation->id,
            ]);

            DB::commit();

            // Send push notification to loueur if enabled
            if ($vehicle->loueur && $vehicle->loueur->getSetting('notify_push', true)) {
                try {
                    $webPush = new WebPushService();
                    $webPush->sendBookingNotification($vehicle->loueur, [
                        'vehicle' => $vehicle->full_name,
                        'dates' => $reservation->start_date->format('d/m') . ' - ' . $reservation->end_date->format('d/m'),
                        'amount' => number_format($totalPrice, 0, ',', ' ') . ' DA',
                        'booking_id' => $reservation->id,
                        'url' => '/loueur/bookings',
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Push notification failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Votre réservation a été enregistrée avec succès.',
                'data' => [
                    'reference' => $reservation->reference,
                    'vehicle' => $vehicle->full_name,
                    'start_date' => $reservation->start_date->format('d/m/Y'),
                    'end_date' => $reservation->end_date->format('d/m/Y'),
                    'days' => $days,
                    'total_price' => number_format($totalPrice, 0, ',', ' ') . ' DA',
                    'status' => 'En attente de confirmation',
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création de la réservation.',
            ], 500);
        }
    }

    /**
     * Consulter une réservation par référence
     */
    public function show(string $reference): JsonResponse
    {
        $reservation = Reservation::with('vehicle.brand')
            ->where('reference', $reference)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reference' => $reservation->reference,
                'vehicle' => [
                    'name' => $reservation->vehicle->full_name,
                    'brand' => $reservation->vehicle->brand->name,
                    'image' => $reservation->vehicle->image ? asset($reservation->vehicle->image) : null,
                ],
                'client' => [
                    'name' => $reservation->client_name,
                    'phone' => $reservation->client_phone,
                    'email' => $reservation->client_email,
                ],
                'dates' => [
                    'start' => $reservation->start_date->format('d/m/Y'),
                    'end' => $reservation->end_date->format('d/m/Y'),
                    'days' => $reservation->getDurationInDays(),
                ],
                'location' => [
                    'pickup' => $reservation->pickup_location,
                    'return' => $reservation->return_location,
                    'flight' => $reservation->flight_number,
                ],
                'pricing' => [
                    'base' => number_format($reservation->base_price, 0, ',', ' ') . ' DA',
                    'options' => number_format($reservation->options_price, 0, ',', ' ') . ' DA',
                    'discount' => number_format($reservation->discount, 0, ',', ' ') . ' DA',
                    'total' => number_format($reservation->total_price, 0, ',', ' ') . ' DA',
                ],
                'status' => $reservation->status,
                'status_label' => $reservation->status_label,
                'created_at' => $reservation->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Annuler une réservation
     */
    public function cancel(Request $request, string $reference): JsonResponse
    {
        $reservation = Reservation::where('reference', $reference)->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée.',
            ], 404);
        }

        if (!$reservation->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette réservation ne peut plus être annulée.',
            ], 422);
        }

        $reservation->update(['status' => 'cancelled']);

        // Supprimer le blocage de disponibilité
        Availability::where('reservation_id', $reservation->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre réservation a été annulée.',
        ]);
    }
}
