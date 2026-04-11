{{-- Résabot Chatbot --}}
<link rel="stylesheet" href="{{ asset('css/chatbot.css') }}" media="print" onload="this.media='all'">

<!-- Floating Action Button -->
<button type="button" class="resabot-fab resabot-fab--idle" id="resabot-fab" aria-label="Ouvrir le chatbot Résabot">
    <svg class="resabot-icon-chat" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>
    </svg>
    <svg class="resabot-icon-close" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M18 6L6 18M6 6l12 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    </svg>
    <span class="resabot-badge" id="resabot-badge"></span>
</button>

<!-- Chat Window -->
<div class="resabot-window resabot-window--hidden" id="resabot-window" role="dialog" aria-label="Chat avec Résabot">
    <!-- Header -->
    <div class="resabot-header">
        <div class="resabot-header-avatar" aria-hidden="true">🤖</div>
        <div class="resabot-header-info">
            <div class="resabot-header-name">Résabot</div>
            <div class="resabot-header-status">En ligne · Répond instantanément</div>
        </div>
        <button type="button" class="resabot-header-close" id="resabot-close" aria-label="Fermer le chat">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    <!-- Messages -->
    <div class="resabot-messages" id="resabot-messages"></div>

    <!-- Input Area -->
    <div class="resabot-input-area">
        <input type="text" class="resabot-input" id="resabot-input" placeholder="Écris ou parle..." autocomplete="off">
        <button type="button" class="resabot-mic-btn" id="resabot-mic" aria-label="Dicter un message">
            <svg class="resabot-mic-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="19" x2="12" y2="23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="8" y1="23" x2="16" y2="23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <svg class="resabot-mic-stop-icon" viewBox="0 0 24 24" aria-hidden="true" style="display:none">
                <rect x="6" y="6" width="12" height="12" rx="2" fill="currentColor"/>
            </svg>
        </button>
        <button type="button" class="resabot-send-btn" id="resabot-send" aria-label="Envoyer">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
        </button>
    </div>
</div>

<script src="{{ asset('js/chatbot.js') }}" defer></script>
