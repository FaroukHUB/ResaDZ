<?php

namespace App\Mail;

use App\Models\Loueur;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeChauffeurMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Loueur $loueur
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur ResaDZ — Votre espace chauffeur est prêt 🧑‍✈️',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-chauffeur',
            with: [
                'loueur' => $this->loueur,
                'user' => $this->loueur->user,
                'companyName' => Setting::get('company_name', 'ResaDZ'),
                'whatsappNumber' => Setting::get('whatsapp_number', ''),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
