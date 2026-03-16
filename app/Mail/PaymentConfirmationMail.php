<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $paymentType, // 'advance', 'deposit', 'final'
        public float $amount,
        public string $currency = 'DZD'
    ) {}

    public function envelope(): Envelope
    {
        $paymentTypeLabel = match($this->paymentType) {
            'advance' => 'Acompte',
            'deposit' => 'Caution',
            'final' => 'Paiement final',
            default => 'Paiement',
        };

        return new Envelope(
            subject: "{$paymentTypeLabel} confirmé - Réservation {$this->booking->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmation',
            with: [
                'booking' => $this->booking,
                'vehicle' => $this->booking->vehicle,
                'loueur' => $this->booking->loueur,
                'paymentType' => $this->paymentType,
                'amount' => $this->amount,
                'currency' => $this->currency,
            ],
        );
    }
}
