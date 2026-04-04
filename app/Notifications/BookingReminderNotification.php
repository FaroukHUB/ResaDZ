<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification
{

    public function __construct(
        public Booking $booking,
        public string $recipientType = 'client' // 'client' or 'loueur'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $vehicle = $booking->vehicle;

        if ($this->recipientType === 'client') {
            return (new MailMessage)
                ->subject("Rappel : Votre location démarre demain - {$booking->reference}")
                ->greeting("Bonjour {$booking->client_name},")
                ->line('Nous vous rappelons que votre location commence demain.')
                ->line("**Véhicule :** {$vehicle->full_name}")
                ->line("**Date de début :** {$booking->start_date->format('d/m/Y')} à {$booking->pickup_time}")
                ->when($booking->pickup_address, fn ($mail) => $mail->line("**Lieu de prise en charge :** {$booking->pickup_address}"))
                ->line("**Loueur :** {$booking->loueur->company_name}")
                ->when($booking->loueur->phone, fn ($mail) => $mail->line("**Contact :** {$booking->loueur->phone}"))
                ->line('')
                ->line('**À ne pas oublier :**')
                ->line('- Votre permis de conduire valide')
                ->line('- Votre pièce d\'identité')
                ->line('- Le montant de la caution')
                ->action('Voir ma réservation', url("/ma-reservation/{$booking->confirmation_token}"))
                ->line('À demain !');
        }

        // For loueur
        return (new MailMessage)
            ->subject("Rappel : Location demain - {$booking->reference}")
            ->greeting('Bonjour,')
            ->line('Vous avez une location qui démarre demain.')
            ->line("**Véhicule :** {$vehicle->full_name}")
            ->line("**Client :** {$booking->client_name}")
            ->line("**Téléphone :** {$booking->client_phone}")
            ->line("**Date :** {$booking->start_date->format('d/m/Y')} à {$booking->pickup_time}")
            ->when($booking->pickup_address, fn ($mail) => $mail->line("**Lieu :** {$booking->pickup_address}"))
            ->action('Voir la réservation', url('/loueur/bookings/' . $booking->id))
            ->line('Pensez à préparer le véhicule et les documents.');
    }
}
