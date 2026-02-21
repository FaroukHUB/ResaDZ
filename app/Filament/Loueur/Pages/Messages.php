<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Messages extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.loueur.pages.messages';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $title = 'Mes messages';

    protected static ?int $navigationSort = 15;

    public ?int $selectedConversationId = null;
    public string $newMessage = '';
    public string $newSubject = '';
    public string $newCategory = 'general';
    public string $newConversationMessage = '';

    public static function getNavigationBadge(): ?string
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return null;

        $count = Conversation::where('loueur_id', $loueur->id)
            ->where('loueur_unread', true)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function mount(): void
    {
        // Auto-select first conversation if exists
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
        return Conversation::where('loueur_id', $this->getLoueur()->id)
            ->with('latestMessage')
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function getSelectedConversation()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return Conversation::where('loueur_id', $this->getLoueur()->id)
            ->where('id', $this->selectedConversationId)
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

        // Mark as read
        $conversation = $this->getSelectedConversation();
        if ($conversation) {
            $conversation->markAsReadByLoueur();
        }

        $this->dispatch('conversationSelected');
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        $conversation = $this->getSelectedConversation();
        if (!$conversation || $conversation->status !== 'open') {
            Notification::make()
                ->title('Impossible d\'envoyer le message')
                ->body('Cette conversation est fermée.')
                ->danger()
                ->send();
            return;
        }

        $conversation->addMessage(
            content: $this->newMessage,
            senderType: 'loueur',
            senderId: $this->getLoueur()->id
        );

        $this->newMessage = '';

        $this->dispatch('messageSent');

        Notification::make()
            ->title('Message envoyé')
            ->success()
            ->send();
    }

    public function startNewConversation(): void
    {
        if (empty(trim($this->newSubject)) || empty(trim($this->newConversationMessage))) {
            Notification::make()
                ->title('Veuillez remplir tous les champs')
                ->danger()
                ->send();
            return;
        }

        $conversation = Conversation::create([
            'loueur_id' => $this->getLoueur()->id,
            'subject' => $this->newSubject,
            'category' => $this->newCategory,
            'status' => 'open',
            'priority' => 'normal',
            'last_message_at' => now(),
            'admin_unread' => true,
        ]);

        $conversation->addMessage(
            content: $this->newConversationMessage,
            senderType: 'loueur',
            senderId: $this->getLoueur()->id
        );

        $this->selectedConversationId = $conversation->id;
        $this->newSubject = '';
        $this->newConversationMessage = '';
        $this->newCategory = 'general';

        $this->dispatch('close-modal', id: 'new-conversation');
        $this->dispatch('conversationSelected');

        Notification::make()
            ->title('Conversation créée')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'conversations' => $this->getConversations(),
            'selectedConversation' => $this->getSelectedConversation(),
            'messages' => $this->getMessages(),
            'categories' => Conversation::getCategories(),
        ];
    }
}
