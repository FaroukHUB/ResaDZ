<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $recipientType = 'client' // 'client' or 'loueur'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'client'
            ? "Votre réservation a été annulée - {$this->booking->reference}"
            : "Réservation annulée - {$this->booking->reference}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancelled',
            with: [
                'booking' => $this->booking,
                'vehicle' => $this->booking->vehicle,
                'loueur' => $this->booking->loueur,
                'recipientType' => $this->recipientType,
            ],
        );
    }
}
