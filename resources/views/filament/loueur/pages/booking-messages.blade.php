<x-filament-panels::page>
    <style>
        .card-modern {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(99,102,241,0.08);
            overflow: hidden;
        }
        .conversation-item {
            transition: all 0.2s ease;
            cursor: pointer;
            border-left: 4px solid transparent;
        }
        .conversation-item:hover {
            background-color: #FFF7ED;
            transform: translateY(-2px);
        }
        .conversation-item.active {
            background-color: #EEF2FF;
            border-left-color: #6366F1;
        }
        .chat-bubble-sent {
            background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%);
            color: #fff;
            border-radius: 1.25rem 1.25rem 0.375rem 1.25rem;
            box-shadow: 0 4px 12px rgba(99,102,241,0.25);
        }
        .chat-bubble-received {
            background: #F1F5F9;
            color: #1E293B;
            border-radius: 1.25rem 1.25rem 1.25rem 0.375rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 600;
        }
        .btn-modern {
            transition: all 0.2s ease;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
        }
        .btn-gradient-primary {
            background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%);
            color: #fff;
        }
        .btn-gradient-primary:hover {
            box-shadow: 0 8px 25px rgba(99,102,241,0.4);
        }
        .scrollbar-modern::-webkit-scrollbar { width: 6px; }
        .scrollbar-modern::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-modern::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        .scrollbar-modern::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        .sidebar-header {
            background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%);
            position: relative;
            overflow: hidden;
        }
        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        .chat-header-bar {
            background: #fff;
            border-bottom: 2px solid transparent;
            border-image: linear-gradient(90deg, #6366F1, #818CF8, transparent) 1;
        }
        .chat-bg-pattern {
            background-color: #F8FAFC;
            background-image: radial-gradient(circle at 20% 50%, rgba(99,102,241,0.03) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(99,102,241,0.03) 0%, transparent 50%);
        }
        .message-time {
            font-size: 11px;
            color: #94A3B8;
        }
        .input-area-modern {
            background: #fff;
            border-top: 1px solid #F1F5F9;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.03);
        }
        .unread-badge {
            background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%);
            box-shadow: 0 2px 8px rgba(99,102,241,0.35);
            animation: unreadPulse 2s ease-in-out infinite;
        }
        @keyframes unreadPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
            50% { box-shadow: 0 0 0 6px rgba(99,102,241,0); }
        }
        .security-banner {
            background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
            border: 1px solid #FDE68A;
            border-radius: 0.75rem;
        }
        .vehicle-thumb {
            border: 2px solid #EEF2FF;
            box-shadow: 0 2px 8px rgba(99,102,241,0.12);
        }
        .dark .card-modern { background: #1E293B; }
        .dark .conversation-item:hover { background-color: rgba(99,102,241,0.1); }
        .dark .conversation-item.active { background-color: rgba(99,102,241,0.15); border-left-color: #6366F1; }
        .dark .chat-bubble-received { background: #334155; color: #E2E8F0; }
        .dark .chat-header-bar { background: #1E293B; }
        .dark .input-area-modern { background: #1E293B; border-top-color: #334155; }
        .dark .chat-bg-pattern { background-color: #0F172A; background-image: none; }
        .dark .security-banner { background: rgba(245,158,11,0.1); border-color: rgba(245,158,11,0.2); }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" style="height: calc(100vh - 200px); min-height: 550px;">

        {{-- Conversations List --}}
        <div class="lg:col-span-4 xl:col-span-3 card-modern flex flex-col">
            {{-- Header --}}
            <div class="flex-shrink-0 px-5 py-4 sidebar-header">
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg leading-tight">Conversations</h3>
                        <p class="text-white/60 text-xs">Messages de vos clients</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto scrollbar-modern">
                @forelse($conversations as $conversation)
                    <button
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="conversation-item w-full text-left px-4 py-4 border-b border-gray-100 dark:border-gray-700/50 {{ $selectedConversation?->id === $conversation->id ? 'active' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            @if($conversation->booking?->vehicle?->image)
                                <img src="{{ asset('storage/' . $conversation->booking->vehicle->image) }}" alt="" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 vehicle-thumb">
                            @else
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%);">
                                    <x-heroicon-o-user class="w-6 h-6" style="color: #6366F1;" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $conversation->client_display_name }}
                                    </span>
                                    @if($conversation->loueur_unread_count > 0)
                                        <span class="flex-shrink-0 w-5 h-5 unread-badge text-white text-xs font-bold rounded-full flex items-center justify-center">
                                            {{ $conversation->loueur_unread_count > 9 ? '9+' : $conversation->loueur_unread_count }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 flex-shrink-0" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    {{ $conversation->booking?->vehicle?->full_name ?? 'Réservation' }}
                                </p>
                                @if($conversation->latestMessage->first())
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-1.5">
                                        {{ Str::limit($conversation->latestMessage->first()->display_content, 40) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%);">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-10 h-10" style="color: #6366F1;" />
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 font-semibold">Aucune conversation</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Les messages de vos clients apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="lg:col-span-8 xl:col-span-9 card-modern flex flex-col">
            @if($selectedConversation)
                {{-- Chat Header --}}
                <div class="flex-shrink-0 px-6 py-4 chat-header-bar dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
                                <x-heroicon-o-user class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-base">{{ $selectedConversation->client_display_name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    {{ $selectedConversation->booking?->vehicle?->full_name }} &bull; {{ $selectedConversation->booking?->reference }}
                                </p>
                            </div>
                        </div>
                        <a
                            href="{{ route('filament.loueur.resources.bookings.edit', $selectedConversation->booking_id) }}"
                            class="btn-modern flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200"
                            style="color: #6366F1; background: #EEF2FF; border: 1px solid #A5B4FC;"
                            onmouseover="this.style.background='linear-gradient(135deg, #6366F1 0%, #818CF8 100%)'; this.style.color='#fff'; this.style.borderColor='transparent';"
                            onmouseout="this.style.background='#EEF2FF'; this.style.color='#6366F1'; this.style.borderColor='#A5B4FC';"
                        >
                            Voir réservation
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-modern chat-bg-pattern" id="chat-messages" wire:poll.15s>
                    @forelse($messages as $message)
                        @if($message->sender_type === 'loueur')
                            {{-- My message (right) - Orange gradient --}}
                            <div class="flex justify-end">
                                <div class="max-w-[70%]">
                                    <div class="chat-bubble-sent px-5 py-3">
                                        <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->display_content }}</p>
                                    </div>
                                    <p class="message-time mt-1.5 text-right px-2">{{ $message->created_at->format('d/m H:i') }}</p>
                                </div>
                            </div>
                        @else
                            {{-- Client message (left) - Gray --}}
                            <div class="flex justify-start">
                                <div class="flex items-end gap-2">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center mb-5" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%);">
                                        <x-heroicon-o-user class="w-3.5 h-3.5" style="color: #6366F1;" />
                                    </div>
                                    <div class="max-w-[70%]">
                                        <div class="chat-bubble-received px-5 py-3">
                                            <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->display_content }}</p>
                                        </div>
                                        <p class="message-time mt-1.5 px-2">{{ $message->created_at->format('d/m H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%);">
                                <x-heroicon-o-chat-bubble-bottom-center-text class="w-10 h-10" style="color: #6366F1;" />
                            </div>
                            <h4 class="font-bold text-gray-700 dark:text-gray-300 text-base">Aucun message</h4>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Commencez la conversation avec votre client.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Security Info Banner --}}
                <div class="mx-4 mb-2">
                    <div class="security-banner px-4 py-2.5 dark:border-amber-800/30">
                        <p class="text-xs text-amber-700 dark:text-amber-400 text-center flex items-center justify-center gap-2">
                            <x-heroicon-o-shield-check class="w-4 h-4 flex-shrink-0" style="color: #F59E0B;" />
                            Les numéros de téléphone et emails sont masqués pour éviter les transactions hors plateforme.
                        </p>
                    </div>
                </div>

                {{-- Message Input --}}
                <div class="p-4 input-area-modern">
                    <form wire:submit="sendMessage" class="flex items-end gap-3">
                        <div class="flex-1">
                            <textarea
                                wire:model="newMessage"
                                placeholder="Écrivez votre message..."
                                rows="1"
                                class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 dark:focus:ring-orange-800/30 resize-none text-sm"
                                style="min-height: 48px; max-height: 120px; transition: all 0.2s ease;"
                                maxlength="2000"
                                x-on:keydown.enter.prevent="if (!$event.shiftKey) $wire.sendMessage()"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            class="btn-modern flex-shrink-0 h-12 px-6 text-white rounded-2xl flex items-center justify-center gap-2 font-semibold text-sm"
                            style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);"
                        >
                            <span class="hidden sm:inline">Envoyer</span>
                            <x-heroicon-o-paper-airplane class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            @else
                {{-- No conversation selected --}}
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8 chat-bg-pattern">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center mb-6" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%); box-shadow: 0 8px 30px rgba(99,102,241,0.12);">
                        <x-heroicon-o-inbox class="w-12 h-12" style="color: #6366F1;" />
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Sélectionnez une conversation</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">Choisissez une conversation dans la liste pour commencer.</p>
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
