<x-filament-panels::page>
    <style>
        .chat-messages { max-height: calc(100vh - 400px); min-height: 300px; }
        .message-bubble { max-width: 75%; }
        .conversation-item { transition: all 0.15s ease; }
        .conversation-item:hover { background-color: rgb(249 250 251); }
        .conversation-item.active { background-color: rgb(239 246 255); border-left: 3px solid rgb(59 130 246); }
        .dark .conversation-item:hover { background-color: rgb(31 41 55); }
        .dark .conversation-item.active { background-color: rgb(30 58 138 / 0.3); }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Conversations List --}}
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Conversations</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Messages de vos clients</p>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
                @forelse($conversations as $conversation)
                    <button
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="conversation-item w-full text-left px-4 py-3 {{ $selectedConversation?->id === $conversation->id ? 'active' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            @if($conversation->booking?->vehicle?->image)
                                <img src="{{ asset('storage/' . $conversation->booking->vehicle->image) }}" alt="" class="w-12 h-10 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-user class="w-5 h-5 text-gray-400" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $conversation->client_display_name }}
                                    </span>
                                    @if($conversation->loueur_unread_count > 0)
                                        <span class="flex-shrink-0 w-5 h-5 bg-primary-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                            {{ $conversation->loueur_unread_count > 9 ? '9+' : $conversation->loueur_unread_count }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $conversation->booking?->vehicle?->full_name ?? 'Réservation' }}
                                </p>
                                @if($conversation->latestMessage->first())
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-1">
                                        {{ Str::limit($conversation->latestMessage->first()->display_content, 40) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="px-4 py-12 text-center">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune conversation</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Les messages de vos clients apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
            @if($selectedConversation)
                {{-- Chat Header --}}
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                            <x-heroicon-o-user class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $selectedConversation->client_display_name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $selectedConversation->booking?->vehicle?->full_name }} &bull; {{ $selectedConversation->booking?->reference }}
                            </p>
                        </div>
                    </div>
                    <a
                        href="{{ route('filament.loueur.resources.bookings.edit', $selectedConversation->booking_id) }}"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-medium flex items-center gap-1"
                    >
                        Voir réservation
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-4 chat-messages" id="chat-messages" wire:poll.15s>
                    @forelse($messages as $message)
                        @if($message->sender_type === 'loueur')
                            {{-- My message (right) --}}
                            <div class="flex justify-end">
                                <div class="message-bubble bg-primary-600 text-white rounded-2xl rounded-br-md px-4 py-3">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->display_content }}</p>
                                    <p class="text-[11px] text-primary-200 mt-1 text-right">{{ $message->created_at->format('d/m H:i') }}</p>
                                </div>
                            </div>
                        @else
                            {{-- Client message (left) --}}
                            <div class="flex justify-start">
                                <div class="message-bubble">
                                    <div class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-2xl rounded-bl-md px-4 py-3">
                                        <p class="text-sm whitespace-pre-wrap">{{ $message->display_content }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ $message->created_at->format('d/m H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-16 h-16 text-gray-200 dark:text-gray-600 mb-4" />
                            <h4 class="font-semibold text-gray-600 dark:text-gray-400">Aucun message</h4>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Commencez la conversation avec votre client.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Info Banner --}}
                <div class="px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border-t border-amber-100 dark:border-amber-800/30">
                    <p class="text-xs text-amber-700 dark:text-amber-400 text-center">
                        <x-heroicon-o-shield-check class="w-4 h-4 inline mr-1 -mt-0.5" />
                        Les numéros de téléphone et emails sont masqués pour éviter les transactions hors plateforme.
                    </p>
                </div>

                {{-- Message Input --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <form wire:submit="sendMessage" class="flex gap-3">
                        <div class="flex-1">
                            <textarea
                                wire:model="newMessage"
                                placeholder="Écrivez votre message..."
                                rows="1"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 focus:border-primary-500 focus:ring focus:ring-primary-200 dark:focus:ring-primary-800 resize-none px-4 py-3 text-sm"
                                maxlength="2000"
                                x-on:keydown.enter.prevent="if (!$event.shiftKey) $wire.sendMessage()"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            class="px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition flex items-center gap-2"
                        >
                            <span class="hidden sm:inline">Envoyer</span>
                            <x-heroicon-o-paper-airplane class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            @else
                {{-- No conversation selected --}}
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                    <x-heroicon-o-inbox class="w-20 h-20 text-gray-200 dark:text-gray-600 mb-4" />
                    <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Sélectionnez une conversation</h4>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Choisissez une conversation dans la liste pour commencer.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('messageSent', () => {
                const chat = document.getElementById('chat-messages');
                if (chat) {
                    setTimeout(() => chat.scrollTop = chat.scrollHeight, 100);
                }
            });

            Livewire.on('conversationSelected', () => {
                const chat = document.getElementById('chat-messages');
                if (chat) {
                    setTimeout(() => chat.scrollTop = chat.scrollHeight, 100);
                }
            });
        });
    </script>
</x-filament-panels::page>
