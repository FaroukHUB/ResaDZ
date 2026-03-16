<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $paymentType, // 'advance', 'deposit', 'final'
        public float $amount,
        public string $currency = 'DZD'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $currencySymbol = $this->currency === 'EUR' ? '€' : 'DA';

        $paymentTypeLabel = match($this->paymentType) {
            'advance' => 'Acompte',
            'deposit' => 'Caution',
            'final' => 'Paiement final',
            default => 'Paiement',
        };

        return (new MailMessage)
            ->subject("{$paymentTypeLabel} reçu - Réservation #{$booking->reference}")
            ->greeting('Bonjour,')
            ->line("Nous avons bien reçu le {$paymentTypeLabel} pour la réservation #{$booking->reference}.")
            ->line("**Montant :** {$this->amount} {$currencySymbol}")
            ->line("**Véhicule :** {$booking->vehicle->full_name}")
            ->line("**Client :** {$booking->client_name}")
            ->action('Voir la réservation', url('/loueur/bookings/' . $booking->id))
            ->line('Merci de votre confiance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'payment_type' => $this->paymentType,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'type' => 'payment_received',
        ];
    }
}
