<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryZone;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Services\PricingService;
use App\Services\WebPushService;
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

        // Options de location configurées par le loueur (siège bébé, GPS, etc.)
        $rentalOptions = $vehicle->loueur
            ? $vehicle->loueur->getSetting('rental_options', [])
            : [];

        // Timer configuré par le loueur (en heures)
        $timerHours = $vehicle->loueur
            ? $vehicle->loueur->getSetting('reservation_timer_hours', null)
            : null;

        // Return options settings
        $fuelReturnFee = $vehicle->loueur
            ? (float) $vehicle->loueur->getSetting('fuel_return_fee', 0)
            : 0;
        $washReturnFee = $vehicle->loueur
            ? (float) $vehicle->loueur->getSetting('wash_return_fee', 0)
            : 0;
        $returnMarginHours = $vehicle->loueur
            ? (int) $vehicle->loueur->getSetting('return_margin_hours', 2)
            : 2;

        // Deposit settings
        $depositRequired = $vehicle->loueur
            ? $vehicle->loueur->getSetting('deposit_required', false)
            : false;
        $depositPaymentMethods = $vehicle->loueur
            ? $vehicle->loueur->getSetting('deposit_payment_methods', [])
            : [];

        // Operating hours from platform settings
        $operatingHoursStart = (int) Setting::get('operating_hours_start', 7);
        $operatingHoursEnd = (int) Setting::get('operating_hours_end', 21);

        return view('front.pages.booking', compact(
            'vehicle',
            'deliveryZones',
            'rentalOptions',
            'timerHours',
            'fuelReturnFee',
            'washReturnFee',
            'returnMarginHours',
            'depositRequired',
            'depositPaymentMethods',
            'operatingHoursStart',
            'operatingHoursEnd'
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
        $loueur = $vehicle->loueur;
        $selectedOptions = $request->options ?? [];

        // Calculer les jours
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $totalDays = max(1, $start->diffInDays($end));

        // Calculer les frais d'options
        $optionsFees = 0;

        // Options de retour (plein/lavage)
        $fuelReturnFee = $loueur ? (float) $loueur->getSetting('fuel_return_fee', 0) : 0;
        $washReturnFee = $loueur ? (float) $loueur->getSetting('wash_return_fee', 0) : 0;

        if (in_array('Retour sans plein', $selectedOptions) && $fuelReturnFee > 0) {
            $optionsFees += $fuelReturnFee;
        }
        if (in_array('Retour sans lavage', $selectedOptions) && $washReturnFee > 0) {
            $optionsFees += $washReturnFee;
        }

        // Options de location du loueur (siège bébé, GPS, etc.)
        $rentalOptions = $loueur ? $loueur->getSetting('rental_options', []) : [];
        foreach ($rentalOptions as $option) {
            $optionName = $option['name'] ?? '';
            if (in_array($optionName, $selectedOptions)) {
                $isFree = ($option['is_free'] ?? false) || (($option['price'] ?? 0) == 0);
                if (!$isFree) {
                    $price = (float) ($option['price'] ?? 0);
                    $per = $option['per'] ?? 'day';
                    if ($per === 'day') {
                        $optionsFees += $price * $totalDays;
                    } else {
                        $optionsFees += $price;
                    }
                }
            }
        }

        // Calculer le prix de base
        $pricing = $pricingService->calculate(
            vehicle: $vehicle,
            startDate: $request->start_date,
            endDate: $request->end_date,
            pickupZoneId: $request->pickup_zone_id,
            returnZoneId: $request->return_zone_id,
            selectedOptions: [],
            currency: $request->currency ?? 'DZD'
        );

        // Ajouter les frais d'options au total
        $pricing['options_total'] = $optionsFees;
        $pricing['total'] = $pricing['total'] + $optionsFees;
        $pricing['formatted_total'] = number_format($pricing['total'], 0, ',', ' ') . ' ' . ($pricing['currency'] === 'EUR' ? '€' : 'DA');

        return response()->json($pricing);
    }

    /**
     * Enregistre la demande de réservation (formulaire simplifié).
     */
    public function store(Request $request, PricingService $pricingService)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_time' => 'required|string|max:5',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'client_email' => 'required|email|max:255',
            'pickup_zone_id' => 'required|exists:delivery_zones,id',
            'return_zone_id' => 'nullable|exists:delivery_zones,id',
            'same_return_location' => 'nullable|in:0,1',
            'options' => 'nullable|array',
            'currency' => 'nullable|in:DZD,EUR',
            'internal_notes' => 'nullable|string|max:1000',
        ]);

        // Si retour au même endroit, copier la zone de pickup vers return
        $returnZoneId = $request->return_zone_id;
        if ($request->input('same_return_location', '1') === '1') {
            $returnZoneId = $request->pickup_zone_id;
        }

        // Récupérer les noms des zones pour les adresses
        $pickupZone = DeliveryZone::find($request->pickup_zone_id);
        $returnZone = $returnZoneId ? DeliveryZone::find($returnZoneId) : $pickupZone;
        $pickupAddress = $pickupZone?->name ?? '';
        $returnAddress = $returnZone?->name ?? $pickupAddress;

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

        // Calculer l'heure de retour automatiquement (pickup_time + marge)
        $returnMarginHours = $loueur ? (int) $loueur->getSetting('return_margin_hours', 2) : (int) Setting::get('default_return_margin_hours', 2);
        $maxReturnHour = (int) Setting::get('max_return_hour', 22);
        $minReturnHour = (int) Setting::get('min_return_hour', 10);
        $pickupTimeParts = explode(':', $request->pickup_time);
        $returnHour = (int) $pickupTimeParts[0] + $returnMarginHours;
        if ($returnHour > $maxReturnHour) $returnHour = $maxReturnHour;
        if ($returnHour < $minReturnHour) $returnHour = $minReturnHour;
        $returnTime = sprintf('%02d:%02d', $returnHour, $pickupTimeParts[1] ?? 0);

        // Gérer les options sélectionnées
        $selectedOptions = $request->options ?? [];
        $optionsFees = 0;

        // Options de retour (plein/lavage)
        $fuelReturnFee = $loueur ? (float) $loueur->getSetting('fuel_return_fee', 0) : 0;
        $washReturnFee = $loueur ? (float) $loueur->getSetting('wash_return_fee', 0) : 0;

        if (in_array('Retour sans plein', $selectedOptions) && $fuelReturnFee > 0) {
            $optionsFees += $fuelReturnFee;
        }
        if (in_array('Retour sans lavage', $selectedOptions) && $washReturnFee > 0) {
            $optionsFees += $washReturnFee;
        }

        // Options de location du loueur (siège bébé, GPS, etc.)
        $rentalOptions = $loueur ? $loueur->getSetting('rental_options', []) : [];
        foreach ($rentalOptions as $option) {
            $optionName = $option['name'] ?? '';
            if (in_array($optionName, $selectedOptions)) {
                // Si l'option n'est pas gratuite
                $isFree = ($option['is_free'] ?? false) || (($option['price'] ?? 0) == 0);
                if (!$isFree) {
                    $price = (float) ($option['price'] ?? 0);
                    $per = $option['per'] ?? 'day';
                    if ($per === 'day') {
                        $optionsFees += $price * $totalDays;
                    } else {
                        $optionsFees += $price;
                    }
                }
            }
        }

        // Calculer le prix de base (sans les options du loueur, car on les calcule séparément)
        $pricing = $pricingService->calculate(
            vehicle: $vehicle,
            startDate: $request->start_date,
            endDate: $request->end_date,
            pickupZoneId: $request->pickup_zone_id,
            returnZoneId: $returnZoneId,
            selectedOptions: [], // Options calculées manuellement
            currency: $request->currency ?? 'DZD'
        );

        // Timer configuré par le loueur
        $timerHours = $loueur ? $loueur->getSetting('reservation_timer_hours', null) : null;

        // Total avec frais d'options
        $totalPrice = $pricing['total'] + $optionsFees;

        // Créer la réservation
        $booking = Booking::create([
            'loueur_id' => $vehicle->loueur_id,
            'vehicle_id' => $vehicle->id,
            'client_id' => auth()->id(),
            'status' => 'pending',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $pricing['total_days'],
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'client_email' => $request->client_email,
            'pickup_zone_id' => $request->pickup_zone_id,
            'return_zone_id' => $returnZoneId,
            'pickup_address' => $pickupAddress,
            'pickup_time' => $request->pickup_time,
            'return_address' => $returnAddress,
            'return_time' => $returnTime,
            'currency' => $request->currency ?? 'DZD',
            'base_price' => $pricing['base_price'],
            'duration_discount' => $pricing['duration_discount'],
            'season_surcharge' => $pricing['season_surcharge'],
            'delivery_fee' => $pricing['delivery_fee'],
            'return_fee' => $pricing['return_fee'],
            'options_total' => $optionsFees,
            'extra_fees' => 0,
            'selected_options' => $selectedOptions,
            'total_price' => $totalPrice,
            'advance_amount' => $pricing['advance_amount'],
            'advance_status' => $pricing['advance_amount'] > 0 ? 'pending' : null,
            'advance_expires_at' => $timerHours ? now()->addHours($timerHours) : null,
            'deposit_amount' => $pricing['deposit_amount'],
            'deposit_currency' => $pricing['deposit_currency'],
            'payment_status' => 'pending',
            'amount_paid' => 0,
            'amount_remaining' => $totalPrice,
            'internal_notes' => $request->internal_notes,
        ]);

        // Send push notification to loueur if enabled
        if ($loueur && $loueur->getSetting('notify_push', true)) {
            try {
                $webPush = new WebPushService();
                $webPush->sendBookingNotification($loueur, [
                    'vehicle' => $vehicle->full_name,
                    'dates' => $booking->start_date->format('d/m') . ' - ' . $booking->end_date->format('d/m'),
                    'amount' => number_format($totalPrice, 0, ',', ' ') . ' DA',
                    'booking_id' => $booking->id,
                    'url' => '/loueur/bookings/' . $booking->id,
                ]);
            } catch (\Exception $e) {
                // Silently fail - don't block booking creation
                \Log::warning('Push notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('booking.confirmation', $booking->reference);
    }

    /**
     * Page de confirmation après réservation (pour le client juste après avoir soumis).
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

    /**
     * Page de confirmation client avec lien unique (envoyée par email après acceptation du loueur).
     */
    public function clientConfirmation(string $token)
    {
        $booking = Booking::with(['vehicle.brand', 'vehicle.category', 'loueur', 'pickupZone', 'returnZone'])
            ->where('confirmation_token', $token)
            ->firstOrFail();

        // Récupérer les conditions de location du loueur
        $rentalConditions = $booking->loueur ? $booking->loueur->getSetting('rental_conditions', []) : [];

        // Vérifier si des documents sont requis
        $requireDocuments = $booking->loueur ? $booking->loueur->getSetting('require_documents', true) : true;

        // Paramètres d'acompte
        $depositRequired = $booking->loueur ? $booking->loueur->getSetting('deposit_required', false) : false;
        $depositPaymentMethods = $booking->loueur ? $booking->loueur->getSetting('deposit_payment_methods', []) : [];

        return view('front.pages.client-confirmation', compact(
            'booking',
            'rentalConditions',
            'requireDocuments',
            'depositRequired',
            'depositPaymentMethods'
        ));
    }

    /**
     * Upload des documents client (CNI, permis).
     */
    public function uploadDocuments(Request $request, string $token)
    {
        $booking = Booking::where('confirmation_token', $token)->firstOrFail();

        $request->validate([
            'client_id_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'client_license_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'client_license_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $updates = [];

        if ($request->hasFile('client_id_document')) {
            $updates['client_id_document'] = $request->file('client_id_document')->store('bookings/documents', 'public');
        }
        if ($request->hasFile('client_license_front')) {
            $updates['client_license_front'] = $request->file('client_license_front')->store('bookings/documents', 'public');
        }
        if ($request->hasFile('client_license_back')) {
            $updates['client_license_back'] = $request->file('client_license_back')->store('bookings/documents', 'public');
        }

        if (!empty($updates)) {
            $booking->update($updates);
        }

        return back()->with('success', 'Vos documents ont été téléchargés avec succès.');
    }
}
