<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Conversation $conversation,
        public string $messagePreview
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $preview = \Str::limit($this->messagePreview, 200);

        return (new MailMessage)
            ->subject('Nouveau message - ' . Setting::get('company_name', 'ResaDZ') . ': ' . $this->conversation->subject)
            ->greeting('Bonjour ' . ($this->conversation->loueur->company_name ?? '') . ',')
            ->line('Vous avez reçu un nouveau message concernant:')
            ->line('**' . $this->conversation->subject . '**')
            ->line('---')
            ->line($preview)
            ->line('---')
            ->action('Voir la conversation', url('/loueur/messages/' . $this->conversation->id))
            ->line('Merci de votre confiance.')
            ->salutation('L\'équipe ' . Setting::get('company_name', 'ResaDZ'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'subject' => $this->conversation->subject,
            'preview' => \Str::limit($this->messagePreview, 100),
        ];
    }
}
