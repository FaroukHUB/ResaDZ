<?php

namespace App\Filament\Admin\Resources\ConversationResource\Pages;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\Conversation;
use App\Notifications\NewMessageNotification;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    protected static string $view = 'filament.admin.pages.view-conversation';

    public ?string $newMessage = '';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Mark as read by admin
        $this->record->markAsReadByAdmin();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('close')
                ->label('Fermer la conversation')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'open')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()
                        ->title('Conversation fermée')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('reopen')
                ->label('Rouvrir')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => $this->record->status !== 'open')
                ->action(function () {
                    $this->record->update(['status' => 'open']);
                    Notification::make()
                        ->title('Conversation rouverte')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        $message = $this->record->addMessage(
            content: $this->newMessage,
            senderType: 'admin',
            senderId: auth()->id()
        );

        // Send email notification to loueur
        try {
            $loueur = $this->record->loueur;
            if ($loueur->user && $loueur->user->email) {
                $loueur->user->notify(new NewMessageNotification($this->record, $this->newMessage));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send message notification: ' . $e->getMessage());
        }

        $this->newMessage = '';

        // Refresh the record to get new messages
        $this->record->refresh();

        $this->dispatch('messageSent');

        Notification::make()
            ->title('Message envoyé')
            ->success()
            ->send();
    }

    public function applyQuickReply(string $message): void
    {
        $this->newMessage = $message;
    }

    public function closeConversation(): void
    {
        $this->record->update(['status' => 'closed']);

        Notification::make()
            ->title('Conversation fermée')
            ->success()
            ->send();

        $this->record->refresh();
    }

    public function reopenConversation(): void
    {
        $this->record->update(['status' => 'open']);

        Notification::make()
            ->title('Conversation rouverte')
            ->success()
            ->send();

        $this->record->refresh();
    }

    public function deleteConversation(): void
    {
        $this->record->messages()->delete();
        $this->record->delete();

        Notification::make()
            ->title('Conversation supprimée')
            ->success()
            ->send();

        $this->redirect(ConversationResource::getUrl('index'));
    }

    public function getViewData(): array
    {
        return [
            'conversation' => $this->record,
            'messages' => $this->record->messages()->orderBy('created_at', 'asc')->get(),
            'loueur' => $this->record->loueur,
        ];
    }
}
