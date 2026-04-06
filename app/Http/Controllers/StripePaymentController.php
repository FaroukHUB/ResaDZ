<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripePaymentController extends Controller
{
    /**
     * Create a Stripe Checkout session for booking payment.
     */
    public function checkout(Request $request, string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();
        $loueur = $booking->loueur;
        $vehicle = $booking->vehicle;

        if (!$loueur || !$loueur->stripe_account_id || !$loueur->stripe_onboarding_complete) {
            return back()->with('error', 'Le loueur n\'a pas encore configuré le paiement en ligne.');
        }

        // Check loueur payment mode preference
        $paymentMode = $loueur->getSetting('online_payment_mode', 'advance_only');
        if ($paymentMode === 'disabled') {
            return back()->with('error', 'Le paiement en ligne n\'est pas activé pour ce loueur.');
        }
        if ($request->payment_type === 'full' && $paymentMode === 'advance_only') {
            return back()->with('error', 'Ce loueur accepte uniquement le paiement de l\'acompte en ligne.');
        }

        // Anti-doublon : vérifier qu'il n'y a pas déjà une réservation payée pour ces dates
        $conflictingBooking = Booking::where('vehicle_id', $booking->vehicle_id)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['confirmed', 'active'])
            ->where(function ($q) use ($booking) {
                $q->whereBetween('start_date', [$booking->start_date, $booking->end_date])
                  ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date])
                  ->orWhere(function ($q2) use ($booking) {
                      $q2->where('start_date', '<=', $booking->start_date)
                          ->where('end_date', '>=', $booking->end_date);
                  });
            })
            ->exists();

        if ($conflictingBooking) {
            return back()->with('error', 'Ce véhicule a déjà une réservation confirmée pour ces dates. Veuillez contacter le loueur.');
        }

        $request->validate([
            'payment_type' => 'required|in:advance,full',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $isAdvance = $request->payment_type === 'advance';

        // Montant en centimes EUR
        if ($isAdvance) {
            $amountEur = $booking->advance_amount_eur ?: round($booking->advance_amount * 0.0037, 2);
            $description = "Acompte réservation {$booking->reference}";
        } else {
            $amountEur = $booking->total_price_eur ?: round($booking->total_price * 0.0037, 2);
            $description = "Paiement total réservation {$booking->reference}";
        }

        $amountCents = (int) round($amountEur * 100);

        if ($amountCents < 50) {
            return back()->with('error', 'Le montant minimum pour un paiement en ligne est de 0,50€.');
        }

        // Commission ResaDZ : 0% sur acompte, 8% ou 6% sur totalité
        $applicationFee = 0;
        if (!$isAdvance) {
            $commissionRate = $booking->commission_rate ?: 8;
            $applicationFee = (int) round($amountCents * $commissionRate / 100);
        }

        try {
            $sessionParams = [
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $amountCents,
                        'product_data' => [
                            'name' => $vehicle->full_name ?? 'Véhicule ResaDZ',
                            'description' => $description,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'payment_intent_data' => [
                    'application_fee_amount' => $applicationFee,
                    'transfer_data' => [
                        'destination' => $loueur->stripe_account_id,
                    ],
                    'metadata' => [
                        'booking_reference' => $booking->reference,
                        'payment_type' => $isAdvance ? 'advance' : 'full',
                        'loueur_id' => $loueur->id,
                    ],
                ],
                'metadata' => [
                    'booking_reference' => $booking->reference,
                    'payment_type' => $isAdvance ? 'advance' : 'full',
                ],
                'success_url' => route('stripe.payment.success', ['reference' => $booking->reference]) . '?type=' . ($isAdvance ? 'advance' : 'full'),
                'cancel_url' => route('booking.confirmation', $booking->reference),
                'customer_email' => $booking->client_email,
            ];

            $session = \Stripe\Checkout\Session::create($sessionParams);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création du paiement. Réessayez.');
        }
    }

    /**
     * Payment success callback.
     */
    public function success(Request $request, string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();
        $type = $request->get('type', 'advance');
        $booking->load(['vehicle', 'loueur']);

        $companyName = Setting::get('company_name', 'ResaDZ');
        $isAdvance = $type === 'advance';

        if ($isAdvance) {
            $booking->update([
                'advance_status' => 'paid',
                'advance_paid_at' => now(),
                'advance_payment_method' => 'stripe',
                'status' => 'confirmed',
            ]);
            $amount = number_format($booking->advance_amount_eur ?: $booking->advance_amount * 0.0037, 2) . ' €';
            $message = 'Acompte payé avec succès ! Votre réservation est confirmée.';
        } else {
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'amount_paid' => $booking->total_price,
                'amount_remaining' => 0,
                'status' => 'confirmed',
            ]);
            $amount = number_format($booking->total_price_eur ?: $booking->total_price * 0.0037, 2) . ' €';
            $message = 'Paiement total effectué ! Votre réservation est confirmée.';
        }

        $totalEur = number_format($booking->total_price_eur ?: $booking->total_price * 0.0037, 2) . ' €';
        $advanceEur = number_format($booking->advance_amount_eur ?: ($booking->advance_amount ?? 0) * 0.0037, 2) . ' €';
        $remainingEur = number_format(($booking->total_price_eur ?: $booking->total_price * 0.0037) - ($booking->advance_amount_eur ?: ($booking->advance_amount ?? 0) * 0.0037), 2) . ' €';

        // Notify loueur
        try {
            if ($booking->loueur) {
                $booking->loueur->notify(new \App\Notifications\PaymentReceivedNotification(
                    $booking,
                    $isAdvance ? 'advance' : 'final',
                    (float) ($isAdvance ? ($booking->advance_amount_eur ?: $booking->advance_amount) : ($booking->total_price_eur ?: $booking->total_price)),
                    'EUR'
                ));
            }
        } catch (\Exception $e) {
            Log::warning('Loueur payment notification failed: ' . $e->getMessage());
        }

        // Send confirmation email to client
        try {
            if ($booking->client_email) {
                \Illuminate\Support\Facades\Mail::send('emails.payment-confirmed-client', [
                    'companyName' => $companyName,
                    'clientName' => $booking->client_name,
                    'paymentLabel' => $isAdvance ? 'Acompte' : 'Paiement total',
                    'amount' => $amount,
                    'reference' => $booking->reference,
                    'vehicleName' => $booking->vehicle->full_name ?? '',
                    'dates' => ($booking->start_date ? $booking->start_date->format('d/m/Y') : '') . ' au ' . ($booking->end_date ? $booking->end_date->format('d/m/Y') : ''),
                    'totalPrice' => $totalEur,
                    'loueurName' => $booking->loueur->company_name ?? '',
                    'isAdvance' => $isAdvance,
                    'remainingAmount' => $remainingEur,
                ], function ($mail) use ($booking, $companyName, $isAdvance) {
                    $mail->to($booking->client_email)
                        ->subject(($isAdvance ? 'Acompte confirmé' : 'Paiement confirmé') . ' - Réservation ' . $booking->reference . ' - ' . $companyName);
                });
            }
        } catch (\Exception $e) {
            Log::warning('Client payment email failed: ' . $e->getMessage());
        }

        return redirect()->route('booking.confirmation', $booking->reference)
            ->with('success', $message);
    }

    /**
     * Stripe webhook handler.
     */
    public function webhook(Request $request)
    {
        $webhookSecret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            if ($webhookSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } else {
                $event = json_decode($payload);
            }
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        $type = is_object($event) ? ($event->type ?? '') : '';
        $data = is_object($event) ? ($event->data->object ?? null) : null;

        if (!$data) return response('OK', 200);

        switch ($type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($data);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($data);
                break;
        }

        return response('OK', 200);
    }

    private function handleCheckoutCompleted($session): void
    {
        $reference = $session->metadata->booking_reference ?? null;
        $paymentType = $session->metadata->payment_type ?? 'advance';

        if (!$reference) return;

        $booking = Booking::where('reference', $reference)->first();
        if (!$booking) return;

        if ($paymentType === 'advance') {
            $booking->update([
                'advance_status' => 'paid',
                'advance_paid_at' => now(),
                'advance_payment_method' => 'stripe',
                'status' => 'confirmed',
            ]);
        } else {
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'amount_paid' => $booking->total_price,
                'amount_remaining' => 0,
                'status' => 'confirmed',
            ]);
        }

        Log::info("Stripe payment confirmed for booking {$reference} ({$paymentType})");
    }

    private function handlePaymentFailed($paymentIntent): void
    {
        $reference = $paymentIntent->metadata->booking_reference ?? null;
        if ($reference) {
            Log::warning("Stripe payment failed for booking {$reference}");
        }
    }
}
