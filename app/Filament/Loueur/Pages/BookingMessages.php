<?php

namespace App\Filament\Loueur\Pages;

use App\Models\BookingConversation;
use App\Models\BookingMessage;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class BookingMessages extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string $view = 'filament.loueur.pages.booking-messages';

    protected static ?string $navigationLabel = 'Messages clients';

    protected static ?string $title = 'Messages clients';

    protected static ?int $navigationSort = 14;

    protected static ?string $navigationGroup = null;

    public ?int $selectedConversationId = null;
    public string $newMessage = '';

    public static function getNavigationBadge(): ?string
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) {
            return null;
        }

        $count = BookingConversation::where('loueur_id', $loueur->id)
            ->where('loueur_unread_count', '>', 0)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function mount(): void
    {
        $firstConversation = $this->getConversations()->first();
        if ($firstConversation) {
            $this->selectConversation($firstConversation->id);
        }
    }

    public function getLoueur()
    {
        return Auth::user()->loueur;
    }

    public function getConversations()
    {
        return BookingConversation::where('loueur_id', $this->getLoueur()->id)
            ->with(['booking.vehicle', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function getSelectedConversation()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return BookingConversation::where('loueur_id', $this->getLoueur()->id)
            ->where('id', $this->selectedConversationId)
            ->with(['booking.vehicle', 'messages'])
            ->first();
    }

    public function getMessages()
    {
        $conversation = $this->getSelectedConversation();
        if (!$conversation) {
            return collect();
        }

        return $conversation->messages()->orderBy('created_at', 'asc')->get();
    }

    public function selectConversation(int $conversationId): void
    {
        $this->selectedConversationId = $conversationId;

        $conversation = $this->getSelectedConversation();
        if ($conversation) {
            $conversation->markAsReadFor('loueur');
        }

        $this->dispatch('conversationSelected');
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        $conversation = $this->getSelectedConversation();
        if (!$conversation) {
            Notification::make()
                ->title('Erreur')
                ->body('Conversation introuvable.')
                ->danger()
                ->send();
            return;
        }

        BookingMessage::create([
            'booking_conversation_id' => $conversation->id,
            'sender_type' => 'loueur',
            'sender_id' => $this->getLoueur()->id,
            'content' => $this->newMessage,
        ]);

        $this->newMessage = '';

        $this->dispatch('messageSent');

        Notification::make()
            ->title('Message envoyé')
            ->success()
            ->duration(2000)
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'conversations' => $this->getConversations(),
            'selectedConversation' => $this->getSelectedConversation(),
            'messages' => $this->getMessages(),
        ];
    }
}
