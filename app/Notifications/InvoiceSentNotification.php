<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceSentNotification extends Notification
{

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Facture ' . $this->invoice->invoice_number . ' - ' . Setting::get('company_name', 'ResaDZ'))
            ->greeting('Bonjour ' . ($this->invoice->billing_name ?? '') . ',')
            ->line('Veuillez trouver ci-joint votre facture:')
            ->line('**Facture N°:** ' . $this->invoice->invoice_number)
            ->line('**Date d\'émission:** ' . $this->invoice->issue_date->format('d/m/Y'))
            ->line('**Date d\'échéance:** ' . $this->invoice->due_date->format('d/m/Y'))
            ->line('**Montant total:** ' . number_format($this->invoice->total, 2, ',', ' ') . ' DA')
            ->action('Voir ma facture', url('/loueur/invoices/' . $this->invoice->id))
            ->line('Merci de votre confiance.')
            ->salutation('L\'équipe ' . Setting::get('company_name', 'ResaDZ'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total' => $this->invoice->total,
        ];
    }
}
