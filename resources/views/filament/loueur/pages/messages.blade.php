<x-filament-panels::page>
    {{-- Guide Banner --}}
    <div class="mb-4 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-cyan-200 dark:border-cyan-800">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1">
                <p class="font-semibold text-cyan-900 dark:text-cyan-100">Centre de messagerie</p>
                <p class="text-sm text-cyan-700 dark:text-cyan-300 mt-1">
                    Communiquez directement avec l'equipe ResaDZ pour toute question concernant votre compte, vos reservations ou vos paiements.
                    <strong>Temps de reponse moyen : 2-4 heures</strong> (jours ouvrables).
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4" style="height: calc(100vh - 240px); min-height: 550px;">
        {{-- Conversations Sidebar --}}
        <div class="lg:col-span-4 xl:col-span-3 bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="flex-shrink-0 px-4 py-4 bg-gradient-to-r from-primary-500 to-primary-600">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-white text-lg">Messages</h3>
                    <button
                        type="button"
                        x-data
                        x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                        class="group w-10 h-10 bg-white/20 hover:bg-white text-white hover:text-cyan-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:shadow-lg hover:scale-110"
                    >
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conversations List --}}
            <div class="flex-1 overflow-y-auto">
                @forelse($conversations as $conversation)
                    <button
                        type="button"
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="w-full px-4 py-4 text-left border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all
                            {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'bg-primary-50 dark:bg-primary-900/20 border-l-4 border-l-primary-500' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white truncate text-sm {{ $conversation->loueur_unread ? 'font-bold' : '' }}">
                                        {{ $conversation->subject }}
                                    </h4>
                                    @if($conversation->loueur_unread)
                                        <span class="flex-shrink-0 w-3 h-3 bg-primary-500 rounded-full animate-pulse"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1">
                                    @if($conversation->latestMessage)
                                        {{ $conversation->latestMessage->sender_type === 'admin' ? 'Admin: ' : '' }}{{ \Str::limit($conversation->latestMessage->content, 40) }}
                                    @else
                                        Aucun message
                                    @endif
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-medium
                                        {{ $conversation->status === 'open' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $conversation->status === 'open' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $conversation->status === 'open' ? 'Ouvert' : 'Fermé' }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                        {{ $conversation->last_message_at?->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">Aucune conversation</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Contactez notre support</p>
                        <button
                            type="button"
                            x-data
                            x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                            class="group mt-4 px-5 py-2.5 bg-gradient-to-r from-emerald-400 via-cyan-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-500 hover:via-cyan-600 hover:to-blue-700 transition-all duration-300 shadow-md shadow-cyan-500/30 hover:shadow-lg hover:shadow-cyan-500/40 hover:scale-105"
                        >
                            Nouvelle conversation
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="lg:col-span-8 xl:col-span-9 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-xl overflow-hidden flex flex-col">
            @if($selectedConversation)
                {{-- Chat Header --}}
                <div class="flex-shrink-0 px-6 py-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Support ResaDZ</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedConversation->subject }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1
                                {{ $selectedConversation->status === 'open' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                <span class="w-2 h-2 rounded-full {{ $selectedConversation->status === 'open' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ $selectedConversation->status === 'open' ? 'En ligne' : 'Fermé' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-list">
                    @php
                        $lastDate = null;
                    @endphp
                    @foreach($messages as $message)
                        @php
                            $currentDate = $message->created_at->format('Y-m-d');
                            $showDateDivider = $lastDate !== $currentDate;
                            $lastDate = $currentDate;
                        @endphp

                        @if($showDateDivider)
                            <div class="flex items-center justify-center my-6">
                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent"></div>
                                <span class="px-4 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-full shadow-sm border border-gray-200 dark:border-gray-700">
                                    {{ $message->created_at->isToday() ? "Aujourd'hui" : ($message->created_at->isYesterday() ? 'Hier' : $message->created_at->format('d M Y')) }}
                                </span>
                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent"></div>
                            </div>
                        @endif

                        @if($message->is_system_message)
                            <div class="flex justify-center">
                                <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700/50 rounded-full text-sm text-gray-600 dark:text-gray-400 italic">
                                    {{ $message->content }}
                                </div>
                            </div>
                        @else
                            <div class="flex {{ $message->sender_type === 'loueur' ? 'justify-end' : 'justify-start' }} group">
                                @if($message->sender_type === 'admin')
                                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center mr-3 mt-1">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="max-w-[70%]">
                                    <div class="relative {{ $message->sender_type === 'loueur'
                                        ? 'bg-gradient-to-br from-primary-500 to-primary-600 text-white'
                                        : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' }}
                                        px-5 py-3 rounded-2xl {{ $message->sender_type === 'loueur' ? 'rounded-br-md' : 'rounded-bl-md' }}">
                                        <p class="whitespace-pre-wrap text-[15px] leading-relaxed">{{ $message->content }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5 px-2 {{ $message->sender_type === 'loueur' ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                            {{ $message->created_at->format('H:i') }}
                                        </span>
                                        @if($message->sender_type === 'loueur')
                                            @if($message->read_at)
                                                <svg class="w-4 h-4 text-primary-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Input Area --}}
                @if($selectedConversation->status === 'open')
                    <div class="flex-shrink-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                        <form wire:submit.prevent="sendMessage" class="flex items-center gap-4">
                            <div class="flex-1">
                                <textarea
                                    wire:model="newMessage"
                                    placeholder="Tapez votre message..."
                                    rows="1"
                                    class="w-full px-5 py-4 bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 resize-none text-base"
                                    style="min-height: 56px; max-height: 120px;"
                                    x-data
                                    x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                                    x-on:keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); }"
                                ></textarea>
                            </div>
                            <button
                                type="submit"
                                style="background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #3b82f6 100%); box-shadow: 0 8px 25px -5px rgba(6, 182, 212, 0.5);"
                                class="flex-shrink-0 w-16 h-16 text-white rounded-2xl hover:opacity-90 focus:ring-4 focus:ring-blue-300 transition-all duration-200 flex items-center justify-center transform hover:scale-105"
                            >
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-shrink-0 p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Cette conversation est fermée</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Bienvenue dans vos messages</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                            Sélectionnez une conversation ou contactez notre support pour toute question
                        </p>
                        <button
                            type="button"
                            x-data
                            x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                            class="group mt-6 px-8 py-4 bg-gradient-to-r from-emerald-400 via-cyan-500 to-blue-600 text-white font-semibold rounded-2xl hover:from-emerald-500 hover:via-cyan-600 hover:to-blue-700 transition-all duration-300 shadow-lg shadow-cyan-500/40 hover:shadow-xl hover:shadow-cyan-500/50 hover:scale-105 relative overflow-hidden"
                        >
                            <div class="absolute inset-0 bg-gradient-to-t from-white/0 to-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <span class="relative z-10 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Contacter le support
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- New Conversation Modal --}}
    <x-filament::modal id="new-conversation" width="lg">
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <span>Nouvelle conversation</span>
            </div>
        </x-slot>

        <form wire:submit.prevent="startNewConversation" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Catégorie</label>
                <select
                    wire:model="newCategory"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                >
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sujet</label>
                <input
                    type="text"
                    wire:model="newSubject"
                    placeholder="Ex: Question sur mon boost"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Message</label>
                <textarea
                    wire:model="newConversationMessage"
                    placeholder="Décrivez votre demande..."
                    rows="4"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 resize-none"
                    required
                ></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-filament::button type="button" color="gray" x-on:click="$dispatch('close-modal', { id: 'new-conversation' })">
                    Annuler
                </x-filament::button>
                <x-filament::button type="submit" class="bg-gradient-to-r from-primary-500 to-primary-600">
                    Envoyer
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    <script>
        document.addEventListener('livewire:navigated', () => {
            scrollToBottom();
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('messageSent', () => {
                setTimeout(scrollToBottom, 100);
            });
            Livewire.on('conversationSelected', () => {
                setTimeout(scrollToBottom, 100);
            });
        });
        function scrollToBottom() {
            const container = document.getElementById('messages-list');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
        scrollToBottom();
    </script>
</x-filament-panels::page>
