<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use App\Models\Transaction;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Support\Facades\Log;

class CancellationService
{
    /**
     * Cancel a booking.
     */
    public function cancelBooking(
        Booking $booking,
        string $cancelledBy, // 'client', 'loueur', 'system'
        ?string $reason = null,
        bool $forceFullRefund = false
    ): array {
        try {
            $refundAmount = $forceFullRefund
                ? $this->calculateFullRefund($booking)
                : $this->calculateRefundAmount($booking, $cancelledBy);

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy,
                'cancellation_reason' => $reason,
                'refund_amount' => $refundAmount['amount'],
                'refund_status' => $refundAmount['amount'] > 0 ? 'pending' : 'none',
            ]);

            // Notify the other party
            $this->sendCancellationNotifications($booking, $cancelledBy);

            Log::info('Booking cancelled', [
                'booking_id' => $booking->id,
                'cancelled_by' => $cancelledBy,
                'refund_amount' => $refundAmount['amount'],
            ]);

            return [
                'success' => true,
                'refund' => $refundAmount,
                'message' => 'Réservation annulée avec succès.',
            ];
        } catch (\Exception $e) {
            Log::error('Booking cancellation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'annulation.',
            ];
        }
    }

    /**
     * Calculate refund amount based on cancellation policy.
     */
    public function calculateRefundAmount(Booking $booking, string $cancelledBy): array
    {
        $advancePaid = $booking->advance_status === 'paid' ? ($booking->advance_amount ?? 0) : 0;

        // If cancelled by loueur, full refund
        if ($cancelledBy === 'loueur') {
            return [
                'amount' => $advancePaid,
                'percentage' => 100,
                'reason' => 'Annulation par le loueur - remboursement intégral',
            ];
        }

        // Get cancellation policy settings
        $freeCancellationHours = (int) Setting::get('free_cancellation_hours', 48);
        $lateCancellationFeePercent = (int) Setting::get('late_cancellation_fee_percent', 50);

        // Calculate hours until booking start
        $hoursUntilStart = now()->diffInHours($booking->start_date, false);

        // Free cancellation period
        if ($hoursUntilStart >= $freeCancellationHours) {
            return [
                'amount' => $advancePaid,
                'percentage' => 100,
                'reason' => "Annulation gratuite (plus de {$freeCancellationHours}h avant)",
            ];
        }

        // Late cancellation - partial refund
        if ($hoursUntilStart > 0) {
            $refundPercent = 100 - $lateCancellationFeePercent;
            $refundAmount = ($advancePaid * $refundPercent) / 100;

            return [
                'amount' => round($refundAmount, 2),
                'percentage' => $refundPercent,
                'reason' => "Annulation tardive - {$lateCancellationFeePercent}% de frais d'annulation",
            ];
        }

        // Cancelled after start date - no refund
        return [
            'amount' => 0,
            'percentage' => 0,
            'reason' => 'Annulation après la date de début - aucun remboursement',
        ];
    }

    /**
     * Calculate full refund (used for force refunds).
     */
    public function calculateFullRefund(Booking $booking): array
    {
        $advancePaid = $booking->advance_status === 'paid' ? ($booking->advance_amount ?? 0) : 0;

        return [
            'amount' => $advancePaid,
            'percentage' => 100,
            'reason' => 'Remboursement intégral',
        ];
    }

    /**
     * Process refund for a cancelled booking.
     */
    public function processRefund(Booking $booking, string $method, ?string $reference = null): bool
    {
        try {
            $refundAmount = $booking->refund_amount ?? 0;

            if ($refundAmount <= 0) {
                return true; // Nothing to refund
            }

            $booking->update([
                'refund_status' => 'processed',
                'refund_method' => $method,
                'refund_reference' => $reference,
                'refund_processed_at' => now(),
            ]);

            // Create expense transaction for the loueur
            Transaction::create([
                'loueur_id' => $booking->loueur_id,
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'type' => 'expense',
                'amount' => $refundAmount,
                'currency' => $booking->currency ?? 'DZD',
                'description' => "Remboursement réservation #{$booking->reference}",
                'payment_method' => $method,
                'payment_reference' => $reference,
                'date' => now()->toDateString(),
            ]);

            Log::info('Refund processed', [
                'booking_id' => $booking->id,
                'amount' => $refundAmount,
                'method' => $method,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send cancellation notifications.
     */
    private function sendCancellationNotifications(Booking $booking, string $cancelledBy): void
    {
        try {
            // Notify loueur if cancelled by client
            if ($cancelledBy === 'client' && $booking->loueur) {
                $booking->loueur->notify(new BookingCancelledNotification($booking, 'loueur'));
            }

            // Notify client if cancelled by loueur
            if ($cancelledBy === 'loueur') {
                // Will be handled by email notification
            }
        } catch (\Exception $e) {
            Log::warning('Cancellation notification failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if a booking can be cancelled.
     */
    public function canBeCancelled(Booking $booking): array
    {
        // Cannot cancel already cancelled bookings
        if ($booking->status === 'cancelled') {
            return [
                'allowed' => false,
                'reason' => 'Cette réservation est déjà annulée.',
            ];
        }

        // Cannot cancel completed bookings
        if ($booking->status === 'completed') {
            return [
                'allowed' => false,
                'reason' => 'Cette réservation est terminée.',
            ];
        }

        // Cannot cancel expired bookings
        if ($booking->status === 'expired') {
            return [
                'allowed' => false,
                'reason' => 'Cette réservation a expiré.',
            ];
        }

        // Can cancel if booking hasn't started
        if ($booking->start_date && $booking->start_date->isFuture()) {
            return [
                'allowed' => true,
                'reason' => null,
            ];
        }

        // Can cancel active bookings (early return)
        if ($booking->status === 'active') {
            return [
                'allowed' => true,
                'reason' => 'Retour anticipé - des frais peuvent s\'appliquer.',
            ];
        }

        return [
            'allowed' => false,
            'reason' => 'Cette réservation ne peut pas être annulée.',
        ];
    }
}
