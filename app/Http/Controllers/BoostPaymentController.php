<?php

namespace App\Http\Controllers;

use App\Models\VehicleBoost;
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

        // Convert DZD to USD (approximate rate, should be configured)
        $exchangeRate = config('services.paypal.dzd_to_usd_rate', 0.0074);
        $amountUSD = round($package->price * $exchangeRate, 2);

        // Minimum PayPal amount
        if ($amountUSD < 1) {
            $amountUSD = 1;
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
     * Handle PayPal IPN/Webhook (optional, for server-side verification).
     */
    public function paypalWebhook(Request $request)
    {
        // Log webhook for debugging
        Log::info('PayPal Webhook received', $request->all());

        // Implement webhook verification if needed
        // This is optional but recommended for production

        return response()->json(['status' => 'received']);
    }
}
