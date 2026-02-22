<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $vehicleName = $this->booking->vehicle?->full_name ?? 'votre véhicule';

        return new Envelope(
            subject: 'Votre réservation ' . $this->booking->reference . ' est confirmée - ' . Setting::get('company_name', 'ResaDZ'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmed',
            with: [
                'booking' => $this->booking,
                'confirmationUrl' => $this->booking->getConfirmationUrl(),
                'loueur' => $this->booking->loueur,
                'vehicle' => $this->booking->vehicle,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
