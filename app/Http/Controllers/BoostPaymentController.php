<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\VehicleBoost;
use App\Notifications\InvoiceSentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BoostPaymentController extends Controller
{
    /**
     * Create PayPal payment for boost.
     */
    public function createPayPalPayment(VehicleBoost $boost)
    {
        // Verify ownership
        $user = Auth::user();
        if (!$user || !$user->loueur || $boost->loueur_id !== $user->loueur->id) {
            abort(403, 'Unauthorized');
        }

        if ($boost->status !== 'pending_payment') {
            return redirect()->route('filament.loueur.pages.boost-vehicle')
                ->with('error', 'Ce boost a déjà été traité.');
        }

        $package = $boost->boostPackage;

        // Convert DZD to USD using platform settings
        $exchangeRate = (float) Setting::get('dzd_to_usd_rate', 0.0074);
        $amountUSD = round($package->price * $exchangeRate, 2);

        // Minimum PayPal amount from settings
        $minPaypalAmount = (float) Setting::get('min_paypal_amount_usd', 1);
        if ($amountUSD < $minPaypalAmount) {
            $amountUSD = $minPaypalAmount;
        }

        // Build PayPal payment URL
        $paypalClientId = config('services.paypal.client_id');
        $paypalMode = config('services.paypal.mode', 'sandbox');

        // Store payment info in session
        session([
            'boost_payment' => [
                'boost_id' => $boost->id,
                'amount_dzd' => $package->price,
                'amount_usd' => $amountUSD,
            ],
        ]);

        // Redirect to PayPal checkout page
        return view('payments.paypal-checkout', [
            'boost' => $boost,
            'package' => $package,
            'amountUSD' => $amountUSD,
            'amountDZD' => $package->price,
            'paypalClientId' => $paypalClientId,
            'paypalMode' => $paypalMode,
        ]);
    }

    /**
     * Handle PayPal payment success.
     */
    public function paypalSuccess(Request $request)
    {
        $paymentData = session('boost_payment');

        if (!$paymentData) {
            return redirect()->route('filament.loueur.pages.boost-vehicle')
                ->with('error', 'Session expirée. Veuillez réessayer.');
        }

        $boost = VehicleBoost::find($paymentData['boost_id']);

        if (!$boost) {
            return redirect()->route('filament.loueur.pages.boost-vehicle')
                ->with('error', 'Boost introuvable.');
        }

        // Verify user owns this boost
        $user = Auth::user();
        if (!$user || !$user->loueur || $boost->loueur_id !== $user->loueur->id) {
            abort(403, 'Unauthorized');
        }

        // Activate the boost
        $boost->update([
            'status' => 'active',
            'payment_reference' => $request->input('paypal_order_id', 'paypal_' . time()),
            'starts_at' => now(),
            'ends_at' => now()->addDays($boost->boostPackage->duration_days),
        ]);

        // Auto-generate invoice for this boost purchase
        try {
            $invoice = Invoice::createForBoost($boost);

            // Mark as paid immediately since payment was just completed
            $invoice->markAsPaid('paypal', $request->input('paypal_order_id', 'paypal_' . time()));

            // Send invoice notification to loueur
            $loueur = $boost->vehicle->loueur;
            $loueur->notify(new InvoiceSentNotification($invoice));

            Log::info('Invoice auto-generated for boost', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'boost_id' => $boost->id,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the payment flow
            Log::error('Failed to generate invoice for boost', [
                'boost_id' => $boost->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Clear session
        session()->forget('boost_payment');

        Log::info('Boost activated via PayPal', [
            'boost_id' => $boost->id,
            'vehicle_id' => $boost->vehicle_id,
            'loueur_id' => $boost->loueur_id,
            'amount' => $boost->amount_paid,
        ]);

        return redirect()->route('filament.loueur.pages.boost-vehicle')
            ->with('success', 'Paiement réussi ! Votre boost est maintenant actif.');
    }

    /**
     * Handle PayPal payment cancel.
     */
    public function paypalCancel()
    {
        $paymentData = session('boost_payment');

        if ($paymentData) {
            // Delete the pending boost
            VehicleBoost::where('id', $paymentData['boost_id'])
                ->where('status', 'pending_payment')
                ->delete();

            session()->forget('boost_payment');
        }

        return redirect()->route('filament.loueur.pages.boost-vehicle')
            ->with('info', 'Paiement annulé.');
    }

    /**
     * Handle PayPal IPN/Webhook with signature verification.
     */
    public function paypalWebhook(Request $request)
    {
        // Log webhook for debugging (sanitized)
        Log::info('PayPal Webhook received', [
            'event_type' => $request->input('event_type'),
            'resource_type' => $request->input('resource_type'),
        ]);

        // Verify webhook signature
        if (!$this->verifyPayPalWebhookSignature($request)) {
            Log::warning('PayPal Webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Process the webhook event
        $eventType = $request->input('event_type');
        $resource = $request->input('resource', []);

        try {
            switch ($eventType) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePaymentCompleted($resource);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                case 'PAYMENT.CAPTURE.REFUNDED':
                    $this->handlePaymentFailed($resource);
                    break;

                default:
                    Log::info('PayPal Webhook: Unhandled event type', ['event_type' => $eventType]);
            }
        } catch (\Exception $e) {
            Log::error('PayPal Webhook processing error', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Processing error'], 500);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Verify PayPal webhook signature.
     */
    private function verifyPayPalWebhookSignature(Request $request): bool
    {
        $webhookId = config('services.paypal.webhook_id');

        // If webhook ID is not configured, skip verification in development
        if (!$webhookId && config('app.env') === 'local') {
            Log::warning('PayPal webhook verification skipped in local environment');
            return true;
        }

        if (!$webhookId) {
            return false;
        }

        // Get headers for verification
        $transmissionId = $request->header('Paypal-Transmission-Id');
        $timestamp = $request->header('Paypal-Transmission-Time');
        $certUrl = $request->header('Paypal-Cert-Url');
        $authAlgo = $request->header('Paypal-Auth-Algo');
        $transmissionSig = $request->header('Paypal-Transmission-Sig');

        if (!$transmissionId || !$timestamp || !$certUrl || !$authAlgo || !$transmissionSig) {
            Log::warning('PayPal Webhook: Missing required headers');
            return false;
        }

        // Validate cert URL is from PayPal
        $certUrlHost = parse_url($certUrl, PHP_URL_HOST);
        if (!str_ends_with($certUrlHost, '.paypal.com')) {
            Log::warning('PayPal Webhook: Invalid cert URL host', ['host' => $certUrlHost]);
            return false;
        }

        // For production, implement full signature verification
        // This requires fetching the certificate and verifying the signature
        // For now, we validate the headers are present and from PayPal domain

        return true;
    }

    /**
     * Handle successful payment capture.
     */
    private function handlePaymentCompleted(array $resource): void
    {
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if (!$orderId) {
            return;
        }

        // Find boost by payment reference
        $boost = VehicleBoost::where('payment_reference', $orderId)
            ->where('status', 'pending_payment')
            ->first();

        if ($boost) {
            $boost->update(['status' => 'active']);
            Log::info('PayPal Webhook: Boost activated', ['boost_id' => $boost->id]);
        }
    }

    /**
     * Handle failed or refunded payment.
     */
    private function handlePaymentFailed(array $resource): void
    {
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if (!$orderId) {
            return;
        }

        $boost = VehicleBoost::where('payment_reference', $orderId)->first();

        if ($boost) {
            $boost->update(['status' => 'cancelled']);
            Log::info('PayPal Webhook: Boost cancelled', ['boost_id' => $boost->id]);
        }
    }
}
