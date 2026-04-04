<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientDocumentsUploadedNotification extends Notification
{
    public function __construct(
        public Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $vehicle = $booking->vehicle;
        $companyName = Setting::get('company_name', 'ResaDZ');

        return (new MailMessage)
            ->subject('Documents reçus - Réservation ' . $booking->reference)
            ->greeting('Salam ' . ($notifiable->company_name ?? '') . ',')
            ->line('Le client **' . $booking->client_name . '** a envoyé ses documents pour la réservation **' . $booking->reference . '**.')
            ->line('**Véhicule :** ' . ($vehicle->full_name ?? ''))
            ->line('**Dates :** du ' . $booking->start_date->format('d/m/Y') . ' au ' . $booking->end_date->format('d/m/Y'))
            ->line('---')
            ->line('Vous pouvez consulter les documents dans votre espace loueur.')
            ->action('Voir la réservation', url('/loueur/bookings/' . $booking->id))
            ->salutation('L\'équipe ' . $companyName);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'client_name' => $this->booking->client_name,
            'message' => 'Documents reçus pour la réservation ' . $this->booking->reference,
        ];
    }
}
