<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comment s\'est passée votre location ? - ' . Setting::get('company_name', 'ResaDZ'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-request',
            with: [
                'booking' => $this->booking,
                'reviewUrl' => route('review.create', $this->booking->confirmation_token),
                'loueur' => $this->booking->loueur,
                'vehicle' => $this->booking->vehicle,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
