<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Main Chat Area --}}
        <div class="lg:col-span-3 flex flex-col bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-xl overflow-hidden" style="height: calc(100vh - 200px); min-height: 600px;">

            {{-- Chat Header --}}
            <div class="flex-shrink-0 px-6 py-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        @if($loueur->logo)
                            <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-lg">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-lg font-bold text-white">{{ strtoupper(substr($loueur->company_name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <div>
                            <h2 class="font-bold text-gray-900 dark:text-white">{{ $loueur->company_name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $conversation->subject }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1
                            {{ $conversation->status === 'open' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                            <span class="w-2 h-2 rounded-full {{ $conversation->status === 'open' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                            {{ \App\Models\Conversation::getStatuses()[$conversation->status] ?? $conversation->status }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ match($conversation->priority) {
                                'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400',
                                'high' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400',
                                'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
                            } }}">
                            {{ \App\Models\Conversation::getPriorities()[$conversation->priority] ?? $conversation->priority }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Messages Container --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container">
                @php
                    $lastDate = null;
                @endphp
                @forelse($messages as $message)
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
                        <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }} group">
                            <div class="max-w-[75%] {{ $message->sender_type === 'admin' ? 'order-2' : 'order-1' }}">
                                <div class="relative {{ $message->sender_type === 'admin'
                                    ? 'bg-gradient-to-br from-primary-500 to-primary-600 text-white'
                                    : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' }}
                                    px-5 py-3 rounded-2xl {{ $message->sender_type === 'admin' ? 'rounded-br-md' : 'rounded-bl-md' }}">
                                    <p class="whitespace-pre-wrap text-[15px] leading-relaxed">{{ $message->content }}</p>
                                </div>
                                <div class="flex items-center gap-2 mt-1.5 px-2 {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                        {{ $message->created_at->format('H:i') }}
                                    </span>
                                    @if($message->sender_type === 'admin')
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
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Démarrez la conversation</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Envoyez votre premier message</p>
                    </div>
                @endforelse
            </div>

            {{-- Input Area --}}
            @if($conversation->status === 'open')
                <div class="flex-shrink-0 p-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-100 dark:border-gray-700">
                    {{-- Quick Templates --}}
                    <div class="mb-3 flex flex-wrap gap-2" x-data="{ showTemplates: false }">
                        <button @click="showTemplates = !showTemplates" type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Réponses rapides
                            <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': showTemplates }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="showTemplates" x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="w-full flex flex-wrap gap-2 mt-2">
                            <button type="button" wire:click="applyQuickReply('Merci pour votre message. Nous traitons votre demande.')"
                                class="px-3 py-1.5 text-xs bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 rounded-full hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors">
                                Accusé de réception
                            </button>
                            <button type="button" wire:click="applyQuickReply('Votre boost a été activé avec succès. Votre véhicule bénéficie maintenant d\'une meilleure visibilité.')"
                                class="px-3 py-1.5 text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                                Boost activé
                            </button>
                            <button type="button" wire:click="applyQuickReply('Votre facture est disponible dans la section \"Mes factures\" de votre espace.')"
                                class="px-3 py-1.5 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                Facture dispo
                            </button>
                            <button type="button" wire:click="applyQuickReply('Merci pour votre paiement. Votre facture a été marquée comme payée.')"
                                class="px-3 py-1.5 text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                                Paiement reçu
                            </button>
                            <button type="button" wire:click="applyQuickReply('N\'hésitez pas si vous avez d\'autres questions. Bonne journée !')"
                                class="px-3 py-1.5 text-xs bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                                Clôture
                            </button>
                            <button type="button" wire:click="applyQuickReply('Pourriez-vous nous fournir plus de détails concernant votre demande ?')"
                                class="px-3 py-1.5 text-xs bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                                Demande infos
                            </button>
                        </div>
                    </div>

                    {{-- Message Input --}}
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
                        <span>Conversation fermée</span>
                        <button wire:click="reopenConversation" class="ml-4 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                            Rouvrir
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Loueur Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 bg-gradient-to-br from-primary-500 to-primary-600 text-white">
                    <div class="flex items-center gap-4">
                        @if($loueur->logo)
                            <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-16 h-16 rounded-xl object-cover ring-4 ring-white/20">
                        @else
                            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <span class="text-2xl font-bold text-white">{{ strtoupper(substr($loueur->company_name, 0, 2)) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-lg">{{ $loueur->company_name }}</h3>
                            <p class="text-white/80 text-sm">{{ $loueur->city }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @if($loueur->phone)
                        <a href="tel:{{ $loueur->phone }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $loueur->phone }}</span>
                        </a>
                    @endif
                    @if($loueur->user && $loueur->user->email)
                        <a href="mailto:{{ $loueur->user->email }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 truncate">{{ $loueur->user->email }}</span>
                        </a>
                    @endif
                </div>
                <div class="px-4 pb-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $loueur->vehicles()->count() }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Véhicules</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $loueur->bookings()->count() }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Locations</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($loueur->rating ?? 0, 1) }}</p>
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">Note</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conversation Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <h4 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold mb-3">Détails</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Catégorie</span>
                        <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-400 rounded-full text-xs font-medium">
                            {{ \App\Models\Conversation::getCategories()[$conversation->category] ?? $conversation->category }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Messages</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $messages->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Créé le</span>
                        <span class="text-gray-900 dark:text-white">{{ $conversation->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($conversation->last_message_at)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Dernier msg</span>
                            <span class="text-gray-900 dark:text-white">{{ $conversation->last_message_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <h4 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold mb-3">Actions</h4>
                <div class="space-y-2">
                    @if($conversation->status === 'open')
                        <button wire:click="closeConversation" class="w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm font-medium">Fermer la conversation</span>
                        </button>
                    @endif
                    <a href="{{ route('filament.admin.resources.loueurs.edit', $loueur) }}" class="w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/30 text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-sm font-medium">Voir le profil loueur</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.invoices.create') }}?loueur_id={{ $loueur->id }}" class="w-full flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/30 text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium">Créer une facture</span>
                    </a>

                    {{-- Delete button --}}
                    <button
                        wire:click="deleteConversation"
                        wire:confirm="Êtes-vous sûr de vouloir supprimer cette conversation ? Cette action est irréversible."
                        class="w-full flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span class="text-sm font-medium">Supprimer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            scrollToBottom();
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('messageSent', () => {
                setTimeout(scrollToBottom, 100);
            });
        });
        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
        scrollToBottom();
    </script>
</x-filament-panels::page>
