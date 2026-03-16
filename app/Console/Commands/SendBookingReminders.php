<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use App\Notifications\BookingReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders {--dry-run : Preview without sending}';

    protected $description = 'Send reminder emails to clients and loueurs for bookings starting tomorrow';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $tomorrow = now()->addDay()->startOfDay();
        $dayAfterTomorrow = $tomorrow->copy()->addDay();

        // Find confirmed bookings starting tomorrow
        $bookings = Booking::with(['vehicle', 'loueur'])
            ->where('status', 'confirmed')
            ->whereBetween('start_date', [$tomorrow, $dayAfterTomorrow])
            ->whereNull('reminder_sent_at')
            ->get();

        $this->info("Found {$bookings->count()} booking(s) starting tomorrow.");

        if ($bookings->isEmpty()) {
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($bookings as $booking) {
            if ($dryRun) {
                $this->line("Would send reminder for booking #{$booking->reference} to {$booking->client_email}");
                continue;
            }

            try {
                // Send to client
                if ($booking->client_email) {
                    Mail::to($booking->client_email)->send(new BookingReminderMail($booking));
                }

                // Notify loueur
                if ($booking->loueur) {
                    $booking->loueur->notify(new BookingReminderNotification($booking, 'loueur'));
                }

                // Mark as sent
                $booking->update(['reminder_sent_at' => now()]);

                $sentCount++;
                $this->info("Sent reminder for booking #{$booking->reference}");

            } catch (\Exception $e) {
                Log::error('Failed to send booking reminder', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to send reminder for booking #{$booking->reference}: {$e->getMessage()}");
            }
        }

        if (!$dryRun) {
            $this->info("Sent {$sentCount} reminder(s).");
        }

        return Command::SUCCESS;
    }
}
