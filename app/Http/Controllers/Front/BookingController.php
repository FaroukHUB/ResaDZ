<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryZone;
use App\Models\Vehicle;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(string $slug)
    {
        $vehicle = Vehicle::with(['brand', 'category', 'loueur'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'available')
            ->firstOrFail();

        // Un véhicule sans loueur ne peut pas être réservé
        if (!$vehicle->loueur_id || !$vehicle->loueur) {
            return redirect()->route('vehicles.show', $vehicle->slug)
                ->with('error', 'Ce véhicule n\'est pas disponible à la réservation pour le moment.');
        }

        $deliveryZones = DeliveryZone::where('loueur_id', $vehicle->loueur_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $availableOptions = $vehicle->available_options ?? [];

        // Timer configuré par le loueur (en heures)
        $timerHours = $vehicle->loueur
            ? $vehicle->loueur->getSetting('reservation_timer_hours', null)
            : null;

        return view('front.pages.booking', compact(
            'vehicle',
            'deliveryZones',
            'availableOptions',
            'timerHours'
        ));
    }

    /**
     * Calcul du prix en AJAX (appelé quand le client change les dates/options).
     */
    public function calculatePrice(Request $request, PricingService $pricingService)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_zone_id' => 'nullable|exists:delivery_zones,id',
            'return_zone_id' => 'nullable|exists:delivery_zones,id',
            'options' => 'nullable|array',
            'currency' => 'nullable|in:DZD,EUR',
        ]);

        $vehicle = Vehicle::with('loueur')->findOrFail($request->vehicle_id);

        $pricing = $pricingService->calculate(
            vehicle: $vehicle,
            startDate: $request->start_date,
            endDate: $request->end_date,
            pickupZoneId: $request->pickup_zone_id,
            returnZoneId: $request->return_zone_id,
            selectedOptions: $request->options ?? [],
            currency: $request->currency ?? 'DZD'
        );

        return response()->json($pricing);
    }

    /**
     * Enregistre la réservation.
     */
    public function store(Request $request, PricingService $pricingService)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_time' => 'required|string|max:5',
            'return_time' => 'required|string|max:5',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'client_email' => 'nullable|email|max:255',
            'pickup_zone_id' => 'nullable|exists:delivery_zones,id',
            'return_zone_id' => 'nullable|exists:delivery_zones,id',
            'pickup_address' => 'required|string|max:500',
            'return_address' => 'required|string|max:500',
            'options' => 'nullable|array',
            'currency' => 'nullable|in:DZD,EUR',
            'internal_notes' => 'nullable|string|max:1000',
            'client_id_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'client_license_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'client_license_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $vehicle = Vehicle::with('loueur')->findOrFail($request->vehicle_id);
        $loueur = $vehicle->loueur;

        // Un véhicule sans loueur ne peut pas être réservé
        if (!$vehicle->loueur_id || !$loueur) {
            return back()->withErrors(['vehicle_id' => 'Ce véhicule n\'est pas disponible à la réservation.'])->withInput();
        }

        // Vérifier le nombre minimum de jours (configuré par le loueur)
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $totalDays = max(1, $start->diffInDays($end));

        $minDays = $vehicle->min_rental_days ?? 1;
        $maxDays = $vehicle->max_rental_days;

        if ($totalDays < $minDays) {
            return back()->withErrors(['start_date' => "La durée minimum est de {$minDays} jour(s)."])->withInput();
        }

        if ($maxDays && $totalDays > $maxDays) {
            return back()->withErrors(['end_date' => "La durée maximum est de {$maxDays} jours."])->withInput();
        }

        // Calculer le prix
        $pricing = $pricingService->calculate(
            vehicle: $vehicle,
            startDate: $request->start_date,
            endDate: $request->end_date,
            pickupZoneId: $request->pickup_zone_id,
            returnZoneId: $request->return_zone_id,
            selectedOptions: $request->options ?? [],
            currency: $request->currency ?? 'DZD'
        );

        // Upload des documents client
        $idDocPath = $request->hasFile('client_id_document')
            ? $request->file('client_id_document')->store('bookings/documents', 'public')
            : null;
        $licenseFrontPath = $request->hasFile('client_license_front')
            ? $request->file('client_license_front')->store('bookings/documents', 'public')
            : null;
        $licenseBackPath = $request->hasFile('client_license_back')
            ? $request->file('client_license_back')->store('bookings/documents', 'public')
            : null;

        // Timer configuré par le loueur
        $timerHours = $loueur ? $loueur->getSetting('reservation_timer_hours', null) : null;

        // Créer la réservation
        $booking = Booking::create([
            'loueur_id' => $vehicle->loueur_id,
            'vehicle_id' => $vehicle->id,
            'client_id' => auth()->id(),
            'reference' => 'RES-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $pricing['total_days'],
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'client_email' => $request->client_email,
            'pickup_zone_id' => $request->pickup_zone_id,
            'return_zone_id' => $request->return_zone_id,
            'pickup_address' => $request->pickup_address,
            'pickup_time' => $request->pickup_time,
            'return_address' => $request->return_address,
            'return_time' => $request->return_time,
            'client_id_document' => $idDocPath,
            'client_license_front' => $licenseFrontPath,
            'client_license_back' => $licenseBackPath,
            'currency' => $request->currency ?? 'DZD',
            'base_price' => $pricing['base_price'],
            'duration_discount' => $pricing['duration_discount'],
            'season_surcharge' => $pricing['season_surcharge'],
            'delivery_fee' => $pricing['delivery_fee'],
            'return_fee' => $pricing['return_fee'],
            'options_total' => $pricing['options_total'],
            'selected_options' => $request->options ?? [],
            'total_price' => $pricing['total'],
            'advance_amount' => $pricing['advance_amount'],
            'advance_status' => $pricing['advance_amount'] > 0 ? 'pending' : null,
            'advance_expires_at' => $timerHours ? now()->addHours($timerHours) : null,
            'deposit_amount' => $pricing['deposit_amount'],
            'deposit_currency' => $pricing['deposit_currency'],
            'payment_status' => 'pending',
            'amount_paid' => 0,
            'amount_remaining' => $pricing['total'],
            'internal_notes' => $request->internal_notes,
        ]);

        return redirect()->route('booking.confirmation', $booking->reference);
    }

    /**
     * Page de confirmation après réservation.
     */
    public function confirmation(string $reference)
    {
        $booking = Booking::with(['vehicle.brand', 'vehicle.loueur'])
            ->where('reference', $reference)
            ->firstOrFail();

        $loueur = $booking->vehicle->loueur;
        $timerHours = $loueur ? $loueur->getSetting('reservation_timer_hours', null) : null;

        return view('front.pages.booking-confirmation', compact('booking', 'timerHours'));
    }
}
