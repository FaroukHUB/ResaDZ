<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-200px)] min-h-[500px]">
        {{-- Conversations List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">Conversations</h3>
                <button
                    type="button"
                    x-data
                    x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                    class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                @forelse($conversations as $conversation)
                    <button
                        type="button"
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="w-full p-4 text-left border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition
                            {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white truncate {{ $conversation->loueur_unread ? 'font-bold' : '' }}">
                                        {{ $conversation->subject }}
                                    </h4>
                                    @if($conversation->loueur_unread)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 truncate mt-0.5">
                                    @if($conversation->latestMessage)
                                        {{ \Str::limit($conversation->latestMessage->content, 50) }}
                                    @else
                                        Aucun message
                                    @endif
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $conversation->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $conversation->status === 'open' ? 'Ouvert' : 'Fermé' }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $conversation->last_message_at?->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p>Aucune conversation</p>
                        <button
                            type="button"
                            x-data
                            x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                            class="mt-4 text-blue-600 hover:underline"
                        >
                            Démarrer une conversation
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Messages Area --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden flex flex-col">
            @if($selectedConversation)
                {{-- Conversation Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $selectedConversation->subject }}</h3>
                    <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                        <span class="px-2 py-0.5 rounded-full text-xs
                            {{ $selectedConversation->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $selectedConversation->status === 'open' ? 'Ouvert' : 'Fermé' }}
                        </span>
                        @if($selectedConversation->category)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-700">
                                {{ $categories[$selectedConversation->category] ?? $selectedConversation->category }}
                            </span>
                        @endif
                        <span>Créé le {{ $selectedConversation->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-list">
                    @foreach($messages as $message)
                        <div class="flex {{ $message->sender_type === 'loueur' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%]">
                                @if($message->is_system_message)
                                    <div class="text-center">
                                        <span class="inline-block px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-400">
                                            {{ $message->content }}
                                        </span>
                                    </div>
                                @else
                                    <div class="{{ $message->sender_type === 'loueur'
                                        ? 'bg-blue-600 text-white rounded-2xl rounded-br-none'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-2xl rounded-bl-none' }} px-4 py-3">
                                        <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 {{ $message->sender_type === 'loueur' ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-xs text-gray-400">
                                            {{ $message->sender_type === 'admin' ? 'Admin ResaDZ' : 'Vous' }} - {{ $message->created_at->format('d/m H:i') }}
                                        </span>
                                        @if($message->sender_type === 'loueur' && $message->read_at)
                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Reply Form --}}
                @if($selectedConversation->status === 'open')
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <form wire:submit.prevent="sendMessage" class="flex gap-3">
                            <textarea
                                wire:model="newMessage"
                                placeholder="Écrivez votre message..."
                                rows="2"
                                class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 resize-none"
                            ></textarea>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition self-end"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center text-gray-500">
                        Cette conversation est fermée.
                    </div>
                @endif
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p>Sélectionnez une conversation ou démarrez-en une nouvelle</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- New Conversation Modal --}}
    <x-filament::modal id="new-conversation" width="lg">
        <x-slot name="heading">
            Nouvelle conversation
        </x-slot>

        <form wire:submit.prevent="startNewConversation" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                <select
                    wire:model="newCategory"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                >
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sujet</label>
                <input
                    type="text"
                    wire:model="newSubject"
                    placeholder="Sujet de votre message"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                <textarea
                    wire:model="newMessage"
                    placeholder="Votre message..."
                    rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    required
                ></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <x-filament::button type="button" color="gray" x-on:click="$dispatch('close-modal', { id: 'new-conversation' })">
                    Annuler
                </x-filament::button>
                <x-filament::button type="submit">
                    Envoyer
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>

    <script>
        // Auto-scroll to bottom of messages
        document.addEventListener('livewire:navigated', () => {
            const container = document.getElementById('messages-list');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</x-filament-panels::page>
