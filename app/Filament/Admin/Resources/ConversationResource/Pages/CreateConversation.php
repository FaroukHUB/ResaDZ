<?php

namespace App\Filament\Admin\Resources\ConversationResource\Pages;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\Conversation;
use App\Notifications\NewMessageNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateConversation extends CreateRecord
{
    protected static string $resource = ConversationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $initialMessage = $data['initial_message'] ?? null;
        unset($data['initial_message']);

        $data['last_message_at'] = now();
        $data['loueur_unread'] = true;

        $conversation = Conversation::create($data);

        if ($initialMessage) {
            $conversation->addMessage(
                content: $initialMessage,
                senderType: 'admin',
                senderId: auth()->id()
            );

            // Send email notification
            try {
                $loueur = $conversation->loueur;
                if ($loueur->user && $loueur->user->email) {
                    $loueur->user->notify(new NewMessageNotification($conversation, $initialMessage));
                }
            } catch (\Exception $e) {
                // Log but don't fail
                \Log::warning('Failed to send message notification: ' . $e->getMessage());
            }
        }

        return $conversation;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
