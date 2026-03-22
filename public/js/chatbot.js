/* ============================================
   ResaDZ Chatbot - Résabot (Vanilla JS)
   ============================================ */
(function () {
    'use strict';

    // --- DOM References ---
    var fab = document.getElementById('resabot-fab');
    var badge = document.getElementById('resabot-badge');
    var chatWindow = document.getElementById('resabot-window');
    var messagesContainer = document.getElementById('resabot-messages');
    var inputField = document.getElementById('resabot-input');
    var sendBtn = document.getElementById('resabot-send');
    var closeBtn = document.getElementById('resabot-close');

    if (!fab || !chatWindow) return;

    var STORAGE_KEY = 'resabot_state';
    var isOpen = false;
    var welcomeSent = false;
    var unreadCount = 0;

    // --- Helpers ---
    function getTime() {
        var now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    }

    function scrollToBottom() {
        requestAnimationFrame(function () {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    }

    function updateBadge() {
        if (unreadCount > 0 && !isOpen) {
            badge.textContent = unreadCount;
            badge.classList.add('resabot-badge--visible');
        } else {
            badge.classList.remove('resabot-badge--visible');
            unreadCount = 0;
        }
    }

    // --- Message rendering ---
    function addMessage(text, sender) {
        var wrapper = document.createElement('div');
        wrapper.className = 'resabot-msg resabot-msg--' + sender;

        var bubble = document.createElement('div');
        bubble.className = 'resabot-msg-bubble';
        bubble.textContent = text;

        var time = document.createElement('div');
        time.className = 'resabot-msg-time';
        time.textContent = getTime();

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        messagesContainer.appendChild(wrapper);
        scrollToBottom();
    }

    function addBotMessageWithHTML(html) {
        var wrapper = document.createElement('div');
        wrapper.className = 'resabot-msg resabot-msg--bot';

        var bubble = document.createElement('div');
        bubble.className = 'resabot-msg-bubble';
        bubble.innerHTML = html;

        var time = document.createElement('div');
        time.className = 'resabot-msg-time';
        time.textContent = getTime();

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        messagesContainer.appendChild(wrapper);
        scrollToBottom();
    }

    function addPills(pills) {
        var container = document.createElement('div');
        container.className = 'resabot-pills';

        pills.forEach(function (pill) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'resabot-pill';
            btn.textContent = pill.label;
            btn.addEventListener('click', function () {
                // Remove all pill containers
                var allPills = messagesContainer.querySelectorAll('.resabot-pills');
                allPills.forEach(function (el) { el.remove(); });
                // Send as user message
                addMessage(pill.label, 'user');
                // Trigger response
                if (pill.action) {
                    pill.action();
                }
            });
            container.appendChild(btn);
        });

        messagesContainer.appendChild(container);
        scrollToBottom();
    }

    function addLinkButton(text, href) {
        var wrapper = document.createElement('div');
        wrapper.className = 'resabot-msg resabot-msg--bot';

        var bubble = document.createElement('div');
        bubble.className = 'resabot-msg-bubble';

        var link = document.createElement('a');
        link.href = href;
        link.className = 'resabot-link-btn';
        link.textContent = text;
        link.innerHTML = text + ' <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';

        bubble.appendChild(link);

        var time = document.createElement('div');
        time.className = 'resabot-msg-time';
        time.textContent = getTime();

        wrapper.appendChild(bubble);
        wrapper.appendChild(time);
        messagesContainer.appendChild(wrapper);
        scrollToBottom();
    }

    function showTypingThenDo(callback) {
        var typing = document.createElement('div');
        typing.className = 'resabot-typing';
        typing.id = 'resabot-typing';
        typing.innerHTML = '<span class="resabot-typing-dot"></span><span class="resabot-typing-dot"></span><span class="resabot-typing-dot"></span>';
        messagesContainer.appendChild(typing);
        scrollToBottom();

        setTimeout(function () {
            var el = document.getElementById('resabot-typing');
            if (el) el.remove();
            callback();
        }, 800 + Math.random() * 600);
    }

    // --- Conversation Tree ---
    function handleLouerVoiture() {
        showTypingThenDo(function () {
            addMessage("Super ! \uD83D\uDE97 Dans quelle wilaya tu cherches ?", 'bot');
            addPills([
                { label: "Alger", action: function () { handleWilayaChoice("Alger"); } },
                { label: "Oran", action: function () { handleWilayaChoice("Oran"); } },
                { label: "Constantine", action: function () { handleWilayaChoice("Constantine"); } },
                { label: "Autre", action: function () { handleWilayaChoice("Autre"); } }
            ]);
        });
    }

    function handleWilayaChoice(wilaya) {
        showTypingThenDo(function () {
            addMessage("Parfait ! Clique ici pour voir les voitures disponibles \uD83D\uDC47", 'bot');
            addLinkButton("Voir les v\u00E9hicules \u00E0 " + wilaya, "#");
        });
    }

    function handleInscrireVoiture() {
        showTypingThenDo(function () {
            addMessage("Excellent choix ! \uD83C\uDF89 C'est 100% gratuit. Tu commences \u00E0 recevoir des demandes d\u00E8s que ton v\u00E9hicule est valid\u00E9. Pr\u00EAt \u00E0 commencer ?", 'bot');
            addPills([
                { label: "Oui, je me lance !", action: handleOuiJeMeLance },
                { label: "J'ai des questions", action: handleJaiDesQuestions }
            ]);
        });
    }

    function handleOuiJeMeLance() {
        showTypingThenDo(function () {
            addMessage("G\u00E9nial ! Clique ici pour d\u00E9marrer \uD83D\uDE80", 'bot');
            addLinkButton("Commencer l'inscription", "/loueur");
        });
    }

    function handleJaiDesQuestions() {
        showTypingThenDo(function () {
            addMessage("Pas de souci ! \uD83D\uDE0A \u00C9cris ta question ci-dessous et je ferai de mon mieux pour t'aider.", 'bot');
        });
    }

    function handleCommentCaMarche() {
        showTypingThenDo(function () {
            addMessage("C'est simple en 3 \u00E9tapes \uD83D\uDE0A\n1\uFE0F\u20E3 Tu publies ta voiture gratuitement\n2\uFE0F\u20E3 Les clients r\u00E9servent en ligne\n3\uFE0F\u20E3 Tu confirmes et tu encaisses \uD83D\uDCB0\nTu veux commencer ?", 'bot');
            addPills([
                { label: "Oui, je me lance !", action: handleOuiJeMeLance },
                { label: "J'ai une autre question", action: handleAutreQuestion }
            ]);
        });
    }

    function handleAutreQuestion() {
        showTypingThenDo(function () {
            addMessage("Pas de souci ! \uD83D\uDE0A \u00C9cris ta question ci-dessous et je ferai de mon mieux pour t'aider.", 'bot');
        });
    }

    function handleUnrecognized() {
        showTypingThenDo(function () {
            addMessage("Je ne suis pas encore assez intelligent pour r\u00E9pondre \u00E0 \u00E7a \uD83D\uDE05 Mais tu peux contacter notre \u00E9quipe via la page Messages de ton espace loueur !", 'bot');
        });
    }

    function showWelcome() {
        if (welcomeSent) return;
        welcomeSent = true;

        showTypingThenDo(function () {
            addMessage("Salam ! \uD83D\uDC4B Je suis R\u00E9sabot, l'assistant ResaDZ. Dis-moi comment je peux t'aider aujourd'hui \uD83D\uDE0A", 'bot');
            addPills([
                { label: "\uD83D\uDE97 Louer une voiture", action: handleLouerVoiture },
                { label: "\uD83D\uDCBC Inscrire ma voiture", action: handleInscrireVoiture },
                { label: "\uD83D\uDCB0 Comment \u00E7a marche ?", action: handleCommentCaMarche },
                { label: "\u2753 Autre question", action: handleAutreQuestion }
            ]);

            if (!isOpen) {
                unreadCount++;
                updateBadge();
            }
        });
    }

    // --- Open / Close ---
    function openChat() {
        isOpen = true;
        fab.classList.add('resabot-fab--open');
        fab.classList.remove('resabot-fab--idle');
        chatWindow.classList.remove('resabot-window--hidden', 'resabot-window--closing');
        chatWindow.classList.add('resabot-window--opening');
        unreadCount = 0;
        updateBadge();
        saveState();

        if (!welcomeSent) {
            showWelcome();
        }

        setTimeout(function () {
            inputField.focus();
        }, 350);
    }

    function closeChat() {
        isOpen = false;
        fab.classList.remove('resabot-fab--open');
        chatWindow.classList.remove('resabot-window--opening');
        chatWindow.classList.add('resabot-window--closing');
        saveState();

        setTimeout(function () {
            if (!isOpen) {
                chatWindow.classList.add('resabot-window--hidden');
                chatWindow.classList.remove('resabot-window--closing');
                fab.classList.add('resabot-fab--idle');
            }
        }, 260);
    }

    function toggleChat() {
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    }

    // --- State persistence ---
    function saveState() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({ open: isOpen }));
        } catch (e) { /* silent */ }
    }

    function loadState() {
        try {
            var data = JSON.parse(localStorage.getItem(STORAGE_KEY));
            if (data && data.open === true) {
                return true;
            }
        } catch (e) { /* silent */ }
        return false;
    }

    // --- User input ---
    function handleSend() {
        var text = inputField.value.trim();
        if (!text) return;

        inputField.value = '';
        addMessage(text, 'user');

        // Remove any existing pills
        var allPills = messagesContainer.querySelectorAll('.resabot-pills');
        allPills.forEach(function (el) { el.remove(); });

        // Simple keyword matching
        var lower = text.toLowerCase();
        if (lower.indexOf('louer') !== -1 || lower.indexOf('location') !== -1 || lower.indexOf('voiture') !== -1 || lower.indexOf('cherche') !== -1) {
            handleLouerVoiture();
        } else if (lower.indexOf('inscrire') !== -1 || lower.indexOf('publier') !== -1 || lower.indexOf('ajouter') !== -1 || lower.indexOf('ma voiture') !== -1) {
            handleInscrireVoiture();
        } else if (lower.indexOf('comment') !== -1 || lower.indexOf('marche') !== -1 || lower.indexOf('\u00E9tape') !== -1 || lower.indexOf('fonctionn') !== -1) {
            handleCommentCaMarche();
        } else {
            handleUnrecognized();
        }
    }

    // --- Event Listeners ---
    fab.addEventListener('click', toggleChat);

    closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        closeChat();
    });

    sendBtn.addEventListener('click', handleSend);

    inputField.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    });

    // --- Init ---
    var wasOpen = loadState();

    if (wasOpen) {
        // Restore open state immediately (no animation)
        isOpen = true;
        fab.classList.add('resabot-fab--open');
        fab.classList.remove('resabot-fab--idle');
        chatWindow.classList.remove('resabot-window--hidden');
        chatWindow.classList.add('resabot-window--opening');
        showWelcome();
    } else {
        // Idle pulse animation
        fab.classList.add('resabot-fab--idle');
        chatWindow.classList.add('resabot-window--hidden');

        // Auto welcome after 2 seconds (shows badge if chat is closed)
        setTimeout(function () {
            if (!welcomeSent) {
                showWelcome();
            }
        }, 2000);
    }

})();
