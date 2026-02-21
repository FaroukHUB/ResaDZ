<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Conversation Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $conversation->subject }}</h2>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $loueur->company_name }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $conversation->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ \App\Models\Conversation::getStatuses()[$conversation->status] ?? $conversation->status }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ match($conversation->priority) {
                                'urgent' => 'bg-red-100 text-red-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'normal' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-600'
                            } }}">
                            {{ \App\Models\Conversation::getPriorities()[$conversation->priority] ?? $conversation->priority }}
                        </span>
                        @if($conversation->category)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                {{ \App\Models\Conversation::getCategories()[$conversation->category] ?? $conversation->category }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Créé le {{ $conversation->created_at->format('d/m/Y H:i') }}</p>
                    @if($conversation->last_message_at)
                        <p>Dernier message: {{ $conversation->last_message_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Messages List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Messages ({{ $messages->count() }})</h3>
            </div>

            <div class="p-4 space-y-4 max-h-[500px] overflow-y-auto" id="messages-container">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] {{ $message->sender_type === 'admin' ? 'order-2' : 'order-1' }}">
                            @if($message->is_system_message)
                                <div class="text-center">
                                    <span class="inline-block px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-full text-sm text-gray-600 dark:text-gray-400">
                                        {{ $message->content }}
                                    </span>
                                </div>
                            @else
                                <div class="{{ $message->sender_type === 'admin'
                                    ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-2xl rounded-tl-none' }} px-4 py-3">
                                    <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                                </div>
                                <div class="flex items-center gap-2 mt-1 {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-xs text-gray-400">
                                        {{ $message->sender_name }} - {{ $message->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if($message->sender_type === 'admin' && $message->read_at)
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        Aucun message pour le moment.
                    </div>
                @endforelse
            </div>

            {{-- Reply Form --}}
            @if($conversation->status === 'open')
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
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition self-end"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center text-gray-500">
                    Cette conversation est fermée. Rouvrez-la pour répondre.
                </div>
            @endif
        </div>

        {{-- Loueur Info Sidebar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Informations loueur</h3>
            <div class="flex items-start gap-4">
                @if($loueur->logo)
                    <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-16 h-16 rounded-xl object-cover">
                @else
                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-500">{{ strtoupper(substr($loueur->company_name, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $loueur->company_name }}</h4>
                    <p class="text-sm text-gray-500">{{ $loueur->city }} {{ $loueur->wilaya ? '- ' . $loueur->wilaya : '' }}</p>
                    @if($loueur->phone)
                        <a href="tel:{{ $loueur->phone }}" class="text-sm text-blue-600 hover:underline block mt-1">{{ $loueur->phone }}</a>
                    @endif
                    @if($loueur->user && $loueur->user->email)
                        <a href="mailto:{{ $loueur->user->email }}" class="text-sm text-blue-600 hover:underline block">{{ $loueur->user->email }}</a>
                    @endif
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $loueur->vehicles()->count() }}</p>
                    <p class="text-xs text-gray-500">Véhicules</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $loueur->bookings()->count() }}</p>
                    <p class="text-xs text-gray-500">Réservations</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($loueur->rating, 1) }}</p>
                    <p class="text-xs text-gray-500">Note</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        document.addEventListener('livewire:navigated', () => {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</x-filament-panels::page>
