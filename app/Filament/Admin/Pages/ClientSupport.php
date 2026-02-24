<?php

namespace App\Filament\Admin\Pages;

use App\Models\ClientSupportConversation;
use App\Models\ClientSupportMessage;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ClientSupport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static string $view = 'filament.admin.pages.client-support';

    protected static ?string $navigationLabel = 'Support clients';

    protected static ?string $title = 'Support clients';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationGroup = 'Communication';

    public ?int $selectedConversationId = null;
    public string $newMessage = '';
    public string $statusFilter = 'open';

    public static function getNavigationBadge(): ?string
    {
        $count = ClientSupportConversation::where('admin_unread', true)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function mount(): void
    {
        $firstConversation = $this->getConversations()->first();
        if ($firstConversation) {
            $this->selectConversation($firstConversation->id);
        }
    }

    public function getConversations()
    {
        $query = ClientSupportConversation::with(['booking', 'latestMessage']);

        if ($this->statusFilter === 'open') {
            $query->whereIn('status', ['open', 'pending']);
        } elseif ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderByDesc('admin_unread')
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function getSelectedConversation()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return ClientSupportConversation::with(['booking.vehicle', 'messages'])
            ->find($this->selectedConversationId);
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
            $conversation->markAsReadByAdmin();
        }

        $this->dispatch('conversationSelected');
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->selectedConversationId = null;

        $firstConversation = $this->getConversations()->first();
        if ($firstConversation) {
            $this->selectConversation($firstConversation->id);
        }
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

        $conversation->addMessage(
            content: $this->newMessage,
            senderType: 'admin',
            senderId: Auth::id()
        );

        $this->newMessage = '';

        $this->dispatch('messageSent');

        Notification::make()
            ->title('Message envoyé')
            ->success()
            ->duration(2000)
            ->send();
    }

    public function updateStatus(string $status): void
    {
        $conversation = $this->getSelectedConversation();
        if (!$conversation) {
            return;
        }

        $conversation->update(['status' => $status]);

        Notification::make()
            ->title('Statut mis à jour')
            ->success()
            ->duration(2000)
            ->send();
    }

    public function updatePriority(string $priority): void
    {
        $conversation = $this->getSelectedConversation();
        if (!$conversation) {
            return;
        }

        $conversation->update(['priority' => $priority]);

        Notification::make()
            ->title('Priorité mise à jour')
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
