/* ============================================
   ResaDZ Chatbot - Résabot (Vanilla JS)
   Powered by Claude AI
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

    var micBtn = document.getElementById('resabot-mic');

    if (!fab || !chatWindow) return;

    var STORAGE_KEY = 'resabot_state';
    var isOpen = false;
    var welcomeSent = false;
    var unreadCount = 0;
    var isWaitingForAI = false;
    var conversationHistory = [];
    var isListening = false;
    var recognition = null;

    // --- CSRF Token ---
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

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

    // --- Convert markdown-style links to HTML ---
    function formatBotMessage(text) {
        // Convert /path links to clickable links
        text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="resabot-link-btn" style="display:inline">$1</a>');
        // Convert bare /path references to links
        text = text.replace(/(^|\s)(\/[a-z-]+(?:\?[^\s]*)?)/gi, function (match, prefix, path) {
            return prefix + '<a href="' + path + '" class="resabot-inline-link">' + path + '</a>';
        });
        // Convert newlines to <br>
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    // --- Message rendering ---
    function addMessage(text, sender) {
        var wrapper = document.createElement('div');
        wrapper.className = 'resabot-msg resabot-msg--' + sender;

        var bubble = document.createElement('div');
        bubble.className = 'resabot-msg-bubble';

        if (sender === 'bot') {
            bubble.innerHTML = formatBotMessage(text);
        } else {
            bubble.textContent = text;
        }

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
                var allPills = messagesContainer.querySelectorAll('.resabot-pills');
                allPills.forEach(function (el) { el.remove(); });
                addMessage(pill.label, 'user');
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

    function showTyping() {
        var typing = document.createElement('div');
        typing.className = 'resabot-typing';
        typing.id = 'resabot-typing';
        typing.innerHTML = '<span class="resabot-typing-dot"></span><span class="resabot-typing-dot"></span><span class="resabot-typing-dot"></span>';
        messagesContainer.appendChild(typing);
        scrollToBottom();
    }

    function hideTyping() {
        var el = document.getElementById('resabot-typing');
        if (el) el.remove();
    }

    function showTypingThenDo(callback) {
        showTyping();
        setTimeout(function () {
            hideTyping();
            callback();
        }, 800 + Math.random() * 600);
    }

    // --- AI Chat via API ---
    function sendToAI(userMessage) {
        if (isWaitingForAI) return;
        isWaitingForAI = true;

        // Add to conversation history
        conversationHistory.push({ role: 'user', content: userMessage });

        // Keep only last 10 messages to avoid token limits
        if (conversationHistory.length > 10) {
            conversationHistory = conversationHistory.slice(-10);
        }

        showTyping();

        fetch('/api/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                message: userMessage,
                history: conversationHistory.slice(0, -1) // Send history without current message
            })
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            hideTyping();
            var reply = data.reply || "D\u00E9sol\u00E9, je n'ai pas compris. R\u00E9essaie !";
            addMessage(reply, 'bot');
            conversationHistory.push({ role: 'assistant', content: reply });
            isWaitingForAI = false;
        })
        .catch(function () {
            hideTyping();
            addMessage("Oups, probl\u00E8me de connexion. R\u00E9essaie dans un instant !", 'bot');
            isWaitingForAI = false;
        });
    }

    // --- Quick Conversation Tree (no AI needed) ---
    function handleLouerVoiture() {
        showTypingThenDo(function () {
            addMessage("Super ! \uD83D\uDE97 Dans quelle wilaya tu cherches ?", 'bot');
            addPills([
                { label: "Alger", action: function () { handleWilayaChoice("Alger"); } },
                { label: "Oran", action: function () { handleWilayaChoice("Oran"); } },
                { label: "Constantine", action: function () { handleWilayaChoice("Constantine"); } },
                { label: "Autre wilaya", action: function () { handleWilayaChoice("Autre"); } }
            ]);
        });
    }

    function handleWilayaChoice(wilaya) {
        showTypingThenDo(function () {
            if (wilaya === "Autre") {
                addMessage("Pas de probl\u00E8me ! Clique ici pour voir tous les v\u00E9hicules et filtre par ta wilaya \uD83D\uDC47", 'bot');
                addLinkButton("Voir tous les v\u00E9hicules", "/vehicules");
            } else {
                addMessage("Parfait ! Voici les voitures disponibles \u00E0 " + wilaya + " \uD83D\uDC47", 'bot');
                addLinkButton("Voir les v\u00E9hicules \u00E0 " + wilaya, "/vehicules?wilaya=" + encodeURIComponent(wilaya));
            }
        });
    }

    function handleInscrireVoiture() {
        showTypingThenDo(function () {
            addMessage("Excellent choix ! \uD83C\uDF89 C'est 100% gratuit. Tu commences \u00E0 recevoir des demandes d\u00E8s que ton v\u00E9hicule est valid\u00E9. Pr\u00EAt \u00E0 commencer ?", 'bot');
            addPills([
                { label: "Oui, je me lance !", action: function () {
                    showTypingThenDo(function () {
                        addMessage("G\u00E9nial ! Clique ici pour d\u00E9marrer \uD83D\uDE80", 'bot');
                        addLinkButton("Commencer l'inscription", "/loueur");
                    });
                }},
                { label: "J'ai des questions", action: function () {
                    showTypingThenDo(function () {
                        addMessage("Pas de souci ! \uD83D\uDE0A \u00C9cris ta question ci-dessous et je ferai de mon mieux pour t'aider.", 'bot');
                    });
                }}
            ]);
        });
    }

    function handleCommentCaMarche() {
        showTypingThenDo(function () {
            addMessage("C'est simple en 3 \u00E9tapes \uD83D\uDE0A\n1\uFE0F\u20E3 Tu publies ta voiture gratuitement\n2\uFE0F\u20E3 Les clients r\u00E9servent en ligne\n3\uFE0F\u20E3 Tu confirmes et tu encaisses \uD83D\uDCB0\nTu veux en savoir plus ?", 'bot');
            addPills([
                { label: "Voir la page d\u00E9taill\u00E9e", action: function () {
                    showTypingThenDo(function () {
                        addLinkButton("Comment \u00E7a marche", "/comment-ca-marche");
                    });
                }},
                { label: "Autre question", action: function () {
                    showTypingThenDo(function () {
                        addMessage("Vas-y, pose ta question ! \uD83D\uDE0A", 'bot');
                    });
                }}
            ]);
        });
    }

    function showWelcome() {
        if (welcomeSent) return;
        welcomeSent = true;

        showTypingThenDo(function () {
            addMessage("Salam ! \uD83D\uDC4B Je suis R\u00E9sabot, l'assistant intelligent de ResaDZ. Pose-moi n'importe quelle question ou choisis un sujet \uD83D\uDC47", 'bot');
            addPills([
                { label: "\uD83D\uDE97 Louer une voiture", action: handleLouerVoiture },
                { label: "\uD83D\uDCBC Inscrire ma voiture", action: handleInscrireVoiture },
                { label: "\uD83D\uDCB0 Comment \u00E7a marche ?", action: handleCommentCaMarche },
                { label: "\u2753 Autre question", action: function () {
                    showTypingThenDo(function () {
                        addMessage("Vas-y, \u00E9cris ta question ci-dessous ! Je suis l\u00E0 pour t'aider \uD83D\uDE0A", 'bot');
                    });
                }}
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
        if (isOpen) closeChat();
        else openChat();
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
            if (data && data.open === true) return true;
        } catch (e) { /* silent */ }
        return false;
    }

    // --- User input ---
    function handleSend() {
        var text = inputField.value.trim();
        if (!text || isWaitingForAI) return;

        inputField.value = '';
        addMessage(text, 'user');

        // Remove any existing pills
        var allPills = messagesContainer.querySelectorAll('.resabot-pills');
        allPills.forEach(function (el) { el.remove(); });

        // Send to AI for intelligent response
        sendToAI(text);
    }

    // --- Voice Recognition ---
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (SpeechRecognition && micBtn) {
        recognition = new SpeechRecognition();
        recognition.lang = 'fr-FR';
        recognition.continuous = false;
        recognition.interimResults = true;

        recognition.onstart = function () {
            isListening = true;
            micBtn.classList.add('resabot-mic-btn--active');
            micBtn.querySelector('.resabot-mic-icon').style.display = 'none';
            micBtn.querySelector('.resabot-mic-stop-icon').style.display = 'block';
            inputField.placeholder = 'Parle maintenant...';
        };

        recognition.onresult = function (event) {
            var transcript = '';
            for (var i = event.resultIndex; i < event.results.length; i++) {
                transcript += event.results[i][0].transcript;
            }
            inputField.value = transcript;

            // Auto-send on final result
            if (event.results[event.results.length - 1].isFinal) {
                stopListening();
                if (transcript.trim()) {
                    handleSend();
                }
            }
        };

        recognition.onerror = function (event) {
            stopListening();
            if (event.error === 'not-allowed') {
                addMessage("Autorise l'accès au micro dans ton navigateur pour utiliser la dictée vocale.", 'bot');
            }
        };

        recognition.onend = function () {
            stopListening();
        };

        micBtn.addEventListener('click', function () {
            if (isListening) {
                recognition.stop();
                stopListening();
            } else {
                try {
                    recognition.start();
                } catch (e) {
                    // Already started
                }
            }
        });
    } else if (micBtn) {
        // Browser doesn't support speech recognition
        micBtn.style.display = 'none';
    }

    function stopListening() {
        isListening = false;
        if (micBtn) {
            micBtn.classList.remove('resabot-mic-btn--active');
            micBtn.querySelector('.resabot-mic-icon').style.display = 'block';
            micBtn.querySelector('.resabot-mic-stop-icon').style.display = 'none';
        }
        inputField.placeholder = 'Écris ou parle...';
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
        isOpen = true;
        fab.classList.add('resabot-fab--open');
        fab.classList.remove('resabot-fab--idle');
        chatWindow.classList.remove('resabot-window--hidden');
        chatWindow.classList.add('resabot-window--opening');
        showWelcome();
    } else {
        fab.classList.add('resabot-fab--idle');
        chatWindow.classList.add('resabot-window--hidden');

        setTimeout(function () {
            if (!welcomeSent) showWelcome();
        }, 2000);
    }

})();
