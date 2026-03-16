<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process advance payment for a booking.
     */
    public function processAdvancePayment(
        Booking $booking,
        float $amount,
        string $method,
        ?string $reference = null
    ): bool {
        try {
            $booking->update([
                'advance_amount' => $amount,
                'advance_status' => 'paid',
                'advance_payment_method' => $method,
                'advance_paid_at' => now(),
            ]);

            // Create transaction record
            Transaction::create([
                'loueur_id' => $booking->loueur_id,
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'type' => 'income',
                'amount' => $amount,
                'currency' => $booking->currency ?? 'DZD',
                'description' => "Acompte réservation #{$booking->reference}",
                'payment_method' => $method,
                'payment_reference' => $reference,
                'date' => now()->toDateString(),
            ]);

            Log::info('Advance payment processed', [
                'booking_id' => $booking->id,
                'amount' => $amount,
                'method' => $method,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Advance payment failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Process deposit payment for a booking.
     */
    public function processDepositPayment(
        Booking $booking,
        float $amount,
        string $currency = 'DZD',
        ?string $method = null
    ): bool {
        try {
            $booking->update([
                'deposit_amount' => $amount,
                'deposit_currency' => $currency,
                'deposit_status' => 'collected',
                'deposit_collected_at' => now(),
            ]);

            Log::info('Deposit collected', [
                'booking_id' => $booking->id,
                'amount' => $amount,
                'currency' => $currency,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Deposit collection failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Process final payment for a booking.
     */
    public function processFinalPayment(
        Booking $booking,
        float $amount,
        string $method,
        ?string $reference = null
    ): bool {
        try {
            $booking->update([
                'final_payment_amount' => $amount,
                'final_payment_method' => $method,
                'final_payment_paid_at' => now(),
                'payment_status' => 'paid',
            ]);

            // Create transaction record
            Transaction::create([
                'loueur_id' => $booking->loueur_id,
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'type' => 'income',
                'amount' => $amount,
                'currency' => $booking->currency ?? 'DZD',
                'description' => "Paiement final réservation #{$booking->reference}",
                'payment_method' => $method,
                'payment_reference' => $reference,
                'date' => now()->toDateString(),
            ]);

            Log::info('Final payment processed', [
                'booking_id' => $booking->id,
                'amount' => $amount,
                'method' => $method,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Final payment failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Return deposit to client.
     */
    public function returnDeposit(Booking $booking, float $amount, ?string $deductionReason = null): bool
    {
        try {
            $originalDeposit = $booking->deposit_amount ?? 0;
            $deduction = $originalDeposit - $amount;

            $booking->update([
                'deposit_status' => 'returned',
                'deposit_returned_at' => now(),
                'deposit_deduction' => $deduction > 0 ? $deduction : 0,
                'deposit_deduction_reason' => $deductionReason,
            ]);

            Log::info('Deposit returned', [
                'booking_id' => $booking->id,
                'original' => $originalDeposit,
                'returned' => $amount,
                'deduction' => $deduction,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Deposit return failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Calculate remaining balance for a booking.
     */
    public function calculateRemainingBalance(Booking $booking): float
    {
        $totalPrice = $booking->total_price ?? 0;
        $advancePaid = $booking->advance_status === 'paid' ? ($booking->advance_amount ?? 0) : 0;
        $finalPaid = $booking->final_payment_amount ?? 0;

        return max(0, $totalPrice - $advancePaid - $finalPaid);
    }

    /**
     * Check if booking is fully paid.
     */
    public function isFullyPaid(Booking $booking): bool
    {
        return $this->calculateRemainingBalance($booking) <= 0;
    }

    /**
     * Get available payment methods for a loueur.
     */
    public function getAvailablePaymentMethods(int $loueurId): array
    {
        $methods = [];

        // Always available
        $methods[] = [
            'code' => 'cash',
            'name' => 'Espèces',
            'icon' => 'banknotes',
        ];

        // Check loueur settings for enabled payment methods
        $loueur = \App\Models\Loueur::find($loueurId);
        if ($loueur) {
            $paymentMethods = $loueur->payment_methods ?? [];

            if (in_array('cib', $paymentMethods)) {
                $methods[] = [
                    'code' => 'cib',
                    'name' => 'Carte CIB',
                    'icon' => 'credit-card',
                ];
            }

            if (in_array('dahabia', $paymentMethods)) {
                $methods[] = [
                    'code' => 'dahabia',
                    'name' => 'Carte Dahabia',
                    'icon' => 'credit-card',
                ];
            }

            if (in_array('baridimob', $paymentMethods)) {
                $methods[] = [
                    'code' => 'baridimob',
                    'name' => 'BaridiMob',
                    'icon' => 'device-phone-mobile',
                ];
            }

            if ($loueur->paypal_email) {
                $methods[] = [
                    'code' => 'paypal',
                    'name' => 'PayPal',
                    'icon' => 'paypal',
                ];
            }

            if ($loueur->iban || $loueur->wise_email) {
                $methods[] = [
                    'code' => 'bank_transfer',
                    'name' => 'Virement bancaire',
                    'icon' => 'building-library',
                ];
            }
        }

        return $methods;
    }

    /**
     * Convert amount between currencies.
     */
    public function convertCurrency(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = (float) Setting::get('eur_to_dzd_rate', 150);

        if ($from === 'EUR' && $to === 'DZD') {
            return $amount * $rate;
        }

        if ($from === 'DZD' && $to === 'EUR') {
            return $rate > 0 ? $amount / $rate : 0;
        }

        return $amount;
    }
}
