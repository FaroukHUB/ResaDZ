<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $vehicle = $booking->vehicle;
        $companyName = Setting::get('company_name', 'ResaDZ');

        $startDate = $booking->start_date->format('d/m/Y');
        $endDate = $booking->end_date->format('d/m/Y');
        $totalDays = $booking->total_days;
        $totalPrice = number_format($booking->total_price, 0, ',', ' ') . ' ' . ($booking->currency === 'EUR' ? '€' : 'DA');

        return (new MailMessage)
            ->subject('Nouvelle demande de réservation - ' . $vehicle->full_name)
            ->greeting('Bonjour ' . ($notifiable->company_name ?? $notifiable->name ?? '') . ',')
            ->line('Vous avez reçu une nouvelle demande de réservation !')
            ->line('---')
            ->line('**Véhicule :** ' . $vehicle->full_name)
            ->line('**Client :** ' . $booking->client_name)
            ->line('**Téléphone :** ' . $booking->client_phone)
            ->line('**Email :** ' . $booking->client_email)
            ->line('**Dates :** du ' . $startDate . ' au ' . $endDate . ' (' . $totalDays . ' jours)')
            ->line('**Heure de prise en charge :** ' . $booking->pickup_time)
            ->line('**Lieu :** ' . ($booking->pickup_address ?: 'Non spécifié'))
            ->line('**Total :** ' . $totalPrice)
            ->when($booking->advance_amount > 0, function ($mail) use ($booking) {
                $advanceAmount = number_format($booking->advance_amount, 0, ',', ' ') . ' ' . ($booking->currency === 'EUR' ? '€' : 'DA');
                return $mail->line('**Acompte demandé :** ' . $advanceAmount);
            })
            ->line('---')
            ->action('Voir la réservation', url('/loueur/bookings/' . $booking->id))
            ->line('Veuillez examiner cette demande et y répondre rapidement.')
            ->salutation('L\'équipe ' . $companyName);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'vehicle' => $this->booking->vehicle?->full_name,
            'client_name' => $this->booking->client_name,
            'total_price' => $this->booking->total_price,
        ];
    }
}
