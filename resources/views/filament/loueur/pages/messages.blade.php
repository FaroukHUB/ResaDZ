<x-filament-panels::page>
    <style>
        .card-modern {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(99,102,241,0.08);
            overflow: hidden;
        }
        .welcome-banner {
            background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 50%, #EEF2FF 100%);
            border: 1px solid #A5B4FC;
            border-radius: 1rem;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 40%;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 70%);
            border-radius: 50%;
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
        .stat-card {
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(99,102,241,0.08);
            transition: all 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-2px); }
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
        .sidebar-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 20px;
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .chat-header-bar {
            background: #fff;
            border-bottom: 2px solid transparent;
            border-image: linear-gradient(90deg, #6366F1, #818CF8, transparent) 1;
        }
        .unread-pulse {
            animation: unreadPulse 2s ease-in-out infinite;
        }
        @keyframes unreadPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
            50% { box-shadow: 0 0 0 6px rgba(99,102,241,0); }
        }
        .date-divider-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, #CBD5E1, transparent);
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
        .chat-bg-pattern {
            background-color: #F8FAFC;
            background-image: radial-gradient(circle at 20% 50%, rgba(99,102,241,0.03) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(99,102,241,0.03) 0%, transparent 50%),
                              radial-gradient(circle at 50% 80%, rgba(99,102,241,0.02) 0%, transparent 50%);
        }
        .dark .card-modern { background: #1E293B; }
        .dark .conversation-item:hover { background-color: rgba(99,102,241,0.1); }
        .dark .conversation-item.active { background-color: rgba(99,102,241,0.15); border-left-color: #6366F1; }
        .dark .chat-bubble-received { background: #334155; color: #E2E8F0; }
        .dark .welcome-banner { background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(249,115,22,0.08) 100%); border-color: rgba(99,102,241,0.3); }
        .dark .chat-header-bar { background: #1E293B; border-image: linear-gradient(90deg, #6366F1, #818CF8, transparent) 1; }
        .dark .input-area-modern { background: #1E293B; border-top-color: #334155; }
        .dark .chat-bg-pattern { background-color: #0F172A; background-image: none; }
    </style>

    {{-- Guide Banner --}}
    <div class="welcome-banner mb-6 p-5">
        <div class="flex items-start gap-4 relative z-10">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-white" />
            </div>
            <div class="flex-1">
                <p class="font-bold text-gray-900 dark:text-white text-base">Centre de messagerie</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                    Communiquez directement avec l'equipe ResaDZ pour toute question concernant votre compte, vos reservations ou vos paiements.
                    <strong style="color: #6366F1;">Temps de reponse moyen : 2-4 heures</strong> (jours ouvrables).
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" style="height: calc(100vh - 260px); min-height: 550px;">
        {{-- Conversations Sidebar --}}
        <div class="lg:col-span-4 xl:col-span-3 card-modern flex flex-col">
            {{-- Header --}}
            <div class="flex-shrink-0 px-5 py-4 sidebar-header">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-lg leading-tight">Messages</h3>
                            <p class="text-white/60 text-xs">Support ResaDZ</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        x-data
                        x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                        class="btn-modern group w-10 h-10 bg-white/20 hover:bg-white text-white hover:text-orange-600 rounded-xl flex items-center justify-center backdrop-blur-sm"
                        style="transition: all 0.2s ease;"
                        title="Nouvelle conversation"
                    >
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Conversations List --}}
            <div class="flex-1 overflow-y-auto scrollbar-modern">
                @forelse($conversations as $conversation)
                    <button
                        type="button"
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="conversation-item w-full px-4 py-4 text-left border-b border-gray-100 dark:border-gray-700/50
                            {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'active' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 2px 8px rgba(99,102,241,0.25);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white truncate text-sm {{ $conversation->loueur_unread ? 'font-bold' : '' }}">
                                        {{ $conversation->subject }}
                                    </h4>
                                    @if($conversation->loueur_unread)
                                        <span class="flex-shrink-0 w-3 h-3 rounded-full unread-pulse" style="background-color: #6366F1;"></span>
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
                                    <span class="badge-modern
                                        {{ $conversation->status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
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
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%);">
                            <svg class="w-10 h-10" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 font-semibold">Aucune conversation</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Contactez notre support</p>
                        <button
                            type="button"
                            x-data
                            x-on:click="$dispatch('open-modal', { id: 'new-conversation' })"
                            class="btn-modern btn-gradient-primary mt-5 px-6 py-2.5 text-sm font-semibold rounded-xl"
                        >
                            Nouvelle conversation
                        </button>
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
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">Support ResaDZ</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" style="color: #6366F1;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $selectedConversation->subject }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="badge-modern px-3 py-1.5 text-xs
                                {{ $selectedConversation->status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}"
                                style="box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                <span class="w-2 h-2 rounded-full {{ $selectedConversation->status === 'open' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ $selectedConversation->status === 'open' ? 'En ligne' : 'Fermé' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-modern chat-bg-pattern" id="messages-list">
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
                                <div class="flex-1 date-divider-line dark:opacity-30"></div>
                                <span class="px-4 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-full mx-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                    {{ $message->created_at->isToday() ? "Aujourd'hui" : ($message->created_at->isYesterday() ? 'Hier' : $message->created_at->format('d M Y')) }}
                                </span>
                                <div class="flex-1 date-divider-line dark:opacity-30"></div>
                            </div>
                        @endif

                        @if($message->is_system_message)
                            <div class="flex justify-center">
                                <div class="px-4 py-2 bg-white dark:bg-gray-700/50 rounded-full text-sm text-gray-500 dark:text-gray-400 italic" style="box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                                    {{ $message->content }}
                                </div>
                            </div>
                        @else
                            <div class="flex {{ $message->sender_type === 'loueur' ? 'justify-end' : 'justify-start' }} group">
                                @if($message->sender_type === 'admin')
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mr-3 mt-1" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 2px 6px rgba(99,102,241,0.25);">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="max-w-[70%]">
                                    <div class="{{ $message->sender_type === 'loueur' ? 'chat-bubble-sent' : 'chat-bubble-received' }} px-5 py-3">
                                        <p class="whitespace-pre-wrap text-[15px] leading-relaxed">{{ $message->content }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5 px-2 {{ $message->sender_type === 'loueur' ? 'justify-end' : 'justify-start' }}">
                                        <span class="message-time">
                                            {{ $message->created_at->format('H:i') }}
                                        </span>
                                        @if($message->sender_type === 'loueur')
                                            @if($message->read_at)
                                                <svg class="w-4 h-4" style="color: #6366F1;" fill="currentColor" viewBox="0 0 24 24">
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
                    <div class="flex-shrink-0 p-4 input-area-modern">
                        <form wire:submit.prevent="sendMessage" class="flex items-end gap-3">
                            <div class="flex-1">
                                <textarea
                                    wire:model="newMessage"
                                    placeholder="Tapez votre message..."
                                    rows="1"
                                    class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:border-orange-400 focus:ring-2 focus:ring-orange-200 dark:focus:ring-orange-800/30 resize-none text-base"
                                    style="min-height: 52px; max-height: 120px; transition: all 0.2s ease;"
                                    x-data
                                    x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                                    x-on:keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); }"
                                ></textarea>
                            </div>
                            <button
                                type="submit"
                                class="btn-modern flex-shrink-0 w-14 h-14 text-white rounded-2xl flex items-center justify-center"
                                style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 15px rgba(99,102,241,0.4);"
                            >
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-shrink-0 p-5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-center gap-3 text-gray-400 dark:text-gray-500">
                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Cette conversation est fermée</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="flex-1 flex items-center justify-center chat-bg-pattern">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, #EEF2FF 0%, #FFEDD5 100%); box-shadow: 0 8px 30px rgba(99,102,241,0.12);">
                            <svg class="w-12 h-12" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            class="btn-modern btn-gradient-primary mt-6 px-8 py-3.5 font-semibold rounded-2xl relative overflow-hidden group"
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
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-900 dark:text-white">Nouvelle conversation</span>
            </div>
        </x-slot>

        <form wire:submit.prevent="startNewConversation" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Catégorie</label>
                <select
                    wire:model="newCategory"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-orange-300 focus:border-orange-400 transition-all duration-200"
                    style="padding: 0.625rem 1rem;"
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
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-orange-300 focus:border-orange-400 transition-all duration-200"
                    style="padding: 0.625rem 1rem;"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Message</label>
                <textarea
                    wire:model="newConversationMessage"
                    placeholder="Décrivez votre demande..."
                    rows="4"
                    class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-orange-300 focus:border-orange-400 resize-none transition-all duration-200"
                    required
                ></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-filament::button type="button" color="gray" x-on:click="$dispatch('close-modal', { id: 'new-conversation' })">
                    Annuler
                </x-filament::button>
                <x-filament::button type="submit" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
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
