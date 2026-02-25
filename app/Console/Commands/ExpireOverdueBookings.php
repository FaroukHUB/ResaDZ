<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpireOverdueBookings extends Command
{
    protected $signature = 'bookings:expire-overdue';

    protected $description = 'Expire les réservations dont le délai de paiement d\'acompte est dépassé';

    public function handle(): int
    {
        $expiredBookings = Booking::where('status', 'pending')
            ->whereNotNull('advance_expires_at')
            ->where('advance_expires_at', '<', now())
            ->where(function ($query) {
                $query->where('advance_status', 'pending')
                      ->orWhereNull('advance_status');
            })
            ->get();

        $count = 0;

        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'expired',
                'cancellation_reason' => 'Délai de paiement de l\'acompte dépassé (expiration automatique)',
                'cancelled_at' => now(),
                'cancelled_by' => 'system',
            ]);

            $count++;

            $this->line("Réservation {$booking->reference} expirée (délai dépassé depuis {$booking->advance_expires_at->format('d/m/Y H:i')})");
        }

        if ($count > 0) {
            $this->info("{$count} réservation(s) expirée(s).");
        } else {
            $this->info('Aucune réservation à expirer.');
        }

        return self::SUCCESS;
    }
}
