<x-filament-panels::page>
    <style>
        .chat-messages { max-height: calc(100vh - 450px); min-height: 300px; }
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
            {{-- Filters --}}
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 flex-wrap">
                    <button wire:click="setStatusFilter('open')"
                            class="px-3 py-1 text-xs font-medium rounded-full transition {{ $statusFilter === 'open' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200' }}">
                        Ouverts
                    </button>
                    <button wire:click="setStatusFilter('resolved')"
                            class="px-3 py-1 text-xs font-medium rounded-full transition {{ $statusFilter === 'resolved' ? 'bg-success-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200' }}">
                        Résolus
                    </button>
                    <button wire:click="setStatusFilter('closed')"
                            class="px-3 py-1 text-xs font-medium rounded-full transition {{ $statusFilter === 'closed' ? 'bg-gray-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200' }}">
                        Fermés
                    </button>
                    <button wire:click="setStatusFilter('all')"
                            class="px-3 py-1 text-xs font-medium rounded-full transition {{ $statusFilter === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200' }}">
                        Tous
                    </button>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
                @forelse($conversations as $conversation)
                    <button
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="conversation-item w-full text-left px-4 py-3 {{ $selectedConversation?->id === $conversation->id ? 'active' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $conversation->priority_color }}-100 dark:bg-{{ $conversation->priority_color }}-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                <x-heroicon-o-user class="w-5 h-5 text-{{ $conversation->priority_color }}-600 dark:text-{{ $conversation->priority_color }}-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate {{ $conversation->admin_unread ? 'font-bold' : '' }}">
                                        {{ $conversation->client_name }}
                                    </span>
                                    @if($conversation->admin_unread)
                                        <span class="flex-shrink-0 w-2 h-2 bg-danger-600 rounded-full"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate font-medium">
                                    {{ $conversation->subject }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-{{ $conversation->status_color }}-100 dark:bg-{{ $conversation->status_color }}-900/30 text-{{ $conversation->status_color }}-700 dark:text-{{ $conversation->status_color }}-400">
                                        {{ $conversation->status_label }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">{{ $conversation->category_label }}</span>
                                </div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="px-4 py-12 text-center">
                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune conversation</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
            @if($selectedConversation)
                {{-- Chat Header --}}
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                <x-heroicon-o-user class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $selectedConversation->client_name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $selectedConversation->client_email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Status dropdown --}}
                            <select wire:change="updateStatus($event.target.value)" class="text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary-500">
                                <option value="open" {{ $selectedConversation->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                                <option value="pending" {{ $selectedConversation->status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="resolved" {{ $selectedConversation->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                                <option value="closed" {{ $selectedConversation->status === 'closed' ? 'selected' : '' }}>Fermé</option>
                            </select>
                            {{-- Priority dropdown --}}
                            <select wire:change="updatePriority($event.target.value)" class="text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary-500">
                                <option value="low" {{ $selectedConversation->priority === 'low' ? 'selected' : '' }}>Basse</option>
                                <option value="normal" {{ $selectedConversation->priority === 'normal' ? 'selected' : '' }}>Normale</option>
                                <option value="high" {{ $selectedConversation->priority === 'high' ? 'selected' : '' }}>Haute</option>
                                <option value="urgent" {{ $selectedConversation->priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                    </div>
                    {{-- Subject & Booking --}}
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedConversation->subject }}</p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $selectedConversation->category_label }}</span>
                            @if($selectedConversation->booking)
                                <span>&bull;</span>
                                <a href="{{ route('filament.admin.resources.bookings.edit', $selectedConversation->booking_id) }}" class="text-primary-600 hover:text-primary-800">
                                    Réservation {{ $selectedConversation->booking->reference }}
                                </a>
                            @endif
                            <span>&bull;</span>
                            <span>Créé le {{ $selectedConversation->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-4 chat-messages" id="chat-messages" wire:poll.15s>
                    @forelse($messages as $message)
                        @if($message->is_internal_note)
                            {{-- Internal note --}}
                            <div class="flex justify-center">
                                <div class="px-3 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-xs rounded-full">
                                    Note interne: {{ $message->content }}
                                </div>
                            </div>
                        @elseif($message->sender_type === 'admin')
                            {{-- Admin message (right) --}}
                            <div class="flex justify-end">
                                <div class="message-bubble bg-primary-600 text-white rounded-2xl rounded-br-md px-4 py-3">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                                    <p class="text-[11px] text-primary-200 mt-1 text-right">
                                        {{ $message->sender?->name ?? 'Admin' }} &bull; {{ $message->created_at->format('d/m H:i') }}
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- Client message (left) --}}
                            <div class="flex justify-start">
                                <div class="message-bubble">
                                    <div class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-2xl rounded-bl-md px-4 py-3">
                                        <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ $message->created_at->format('d/m H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-16 h-16 text-gray-200 dark:text-gray-600 mb-4" />
                            <p class="text-sm text-gray-400 dark:text-gray-500">Aucun message</p>
                        </div>
                    @endforelse
                </div>

                {{-- Message Input --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <form wire:submit="sendMessage" class="flex gap-3">
                        <div class="flex-1">
                            <textarea
                                wire:model="newMessage"
                                placeholder="Écrivez votre réponse..."
                                rows="2"
                                class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-800 focus:border-primary-500 focus:ring focus:ring-primary-200 dark:focus:ring-primary-800 resize-none px-4 py-3 text-sm"
                                x-on:keydown.enter.prevent="if (!$event.shiftKey) $wire.sendMessage()"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            class="px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition flex items-center gap-2 self-end"
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
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Choisissez une demande de support dans la liste.</p>
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
