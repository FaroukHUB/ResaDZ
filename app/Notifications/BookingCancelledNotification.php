<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $recipientType = 'loueur' // 'loueur' or 'client'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $vehicle = $booking->vehicle;

        if ($this->recipientType === 'loueur') {
            return (new MailMessage)
                ->subject('Réservation annulée - ' . $booking->reference)
                ->greeting('Bonjour,')
                ->line("La réservation #{$booking->reference} a été annulée.")
                ->line("**Véhicule :** {$vehicle->full_name}")
                ->line("**Client :** {$booking->client_name}")
                ->line("**Dates :** {$booking->start_date->format('d/m/Y')} - {$booking->end_date->format('d/m/Y')}")
                ->when($booking->cancellation_reason, fn ($mail) => $mail->line("**Raison :** {$booking->cancellation_reason}"))
                ->action('Voir les détails', url('/loueur/bookings/' . $booking->id))
                ->line('Les dates sont à nouveau disponibles pour ce véhicule.');
        }

        // For client
        return (new MailMessage)
            ->subject('Votre réservation a été annulée - ' . $booking->reference)
            ->greeting("Bonjour {$booking->client_name},")
            ->line("Nous vous informons que votre réservation #{$booking->reference} a été annulée.")
            ->line("**Véhicule :** {$vehicle->full_name}")
            ->line("**Dates :** {$booking->start_date->format('d/m/Y')} - {$booking->end_date->format('d/m/Y')}")
            ->when($booking->refund_amount > 0, fn ($mail) => $mail->line("**Remboursement :** {$booking->refund_amount} " . ($booking->currency ?? 'DA')))
            ->when($booking->cancellation_reason, fn ($mail) => $mail->line("**Raison :** {$booking->cancellation_reason}"))
            ->line('Nous espérons vous revoir bientôt.')
            ->action('Rechercher un autre véhicule', url('/vehicules'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'vehicle_name' => $this->booking->vehicle->full_name ?? '',
            'client_name' => $this->booking->client_name,
            'cancelled_by' => $this->booking->cancelled_by,
            'refund_amount' => $this->booking->refund_amount,
            'type' => 'booking_cancelled',
        ];
    }
}
