{{-- Card d'installation PWA --}}
<div id="pwa-install-card" class="hidden">
    <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-6 shadow-lg relative overflow-hidden">
        {{-- Bouton fermer --}}
        <button onclick="dismissPwaCard()" class="absolute top-3 right-3 text-white/60 hover:text-white transition z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="flex flex-col sm:flex-row items-start gap-5">
            {{-- Icon --}}
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                <span class="text-3xl">📱</span>
            </div>

            <div class="flex-1 min-w-0">
                <h3 class="text-white font-bold text-lg">Installez ResaDZ sur votre appareil</h3>
                <p class="text-white/80 text-sm mt-1">Accédez à votre espace en un clic et recevez les notifications en temps réel</p>

                {{-- Instructions selon OS (une seule visible à la fois) --}}
                <div id="pwa-steps" class="mt-4 space-y-2 text-sm">
                    {{-- Android Chrome --}}
                    <div id="pwa-android" class="hidden">
                        <div class="flex items-center gap-2 text-white/90 mb-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 15.341a1 1 0 0 0-.737-.16 1 1 0 0 0-.618.434c-.102.165-.14.362-.107.554a1 1 0 0 0 .444.667c.164.103.361.14.554.107a1 1 0 0 0 .667-.444 1 1 0 0 0-.203-1.158zM6.863 15.341a1 1 0 0 0-.203 1.158 1 1 0 0 0 .667.444c.193.033.39-.004.554-.107a1 1 0 0 0 .444-.667 1 1 0 0 0-.107-.554 1 1 0 0 0-.618-.434 1 1 0 0 0-.737.16zM17.785 8.563l1.924-3.332a.4.4 0 0 0-.146-.546.4.4 0 0 0-.546.146l-1.948 3.374A11.2 11.2 0 0 0 12.193 7a11.2 11.2 0 0 0-4.876 1.205L5.369 4.83a.4.4 0 0 0-.546-.146.4.4 0 0 0-.146.546l1.924 3.332C3.601 10.267 1.543 13.389 1.2 17h21.986c-.343-3.611-2.401-6.733-5.401-8.437z"/></svg>
                            <span class="font-semibold text-white">Chrome Android</span>
                        </div>
                        <ol class="space-y-1.5 text-white/80">
                            <li>1️⃣ Appuyez sur le menu <strong class="text-white">⋮</strong> en haut à droite de Chrome</li>
                            <li>2️⃣ Sélectionnez <strong class="text-white">"Ajouter à l'écran d'accueil"</strong></li>
                            <li>3️⃣ Appuyez sur <strong class="text-white">"Ajouter"</strong> ✓</li>
                        </ol>
                    </div>

                    {{-- iOS Safari --}}
                    <div id="pwa-ios" class="hidden">
                        <div class="flex items-center gap-2 text-white/90 mb-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                            <span class="font-semibold text-white">Safari (iPhone / iPad)</span>
                        </div>
                        <ol class="space-y-1.5 text-white/80">
                            <li>1️⃣ Appuyez sur le bouton <strong class="text-white">Partager</strong> <span class="text-white">⬆</span> en bas de Safari</li>
                            <li>2️⃣ Sélectionnez <strong class="text-white">"Sur l'écran d'accueil"</strong></li>
                            <li>3️⃣ Appuyez sur <strong class="text-white">"Ajouter"</strong> ✓</li>
                        </ol>
                    </div>

                    {{-- Desktop Chrome --}}
                    <div id="pwa-desktop" class="hidden">
                        <div class="flex items-center gap-2 text-white/90 mb-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <span class="font-semibold text-white">Chrome (PC / Mac)</span>
                        </div>
                        <ol class="space-y-1.5 text-white/80">
                            <li>1️⃣ Cliquez sur l'icône <strong class="text-white">⊕</strong> dans la barre d'adresse à droite</li>
                            <li>2️⃣ Cliquez sur <strong class="text-white">"Installer"</strong> ✓</li>
                        </ol>
                    </div>
                </div>

                {{-- Bouton installer (pour les navigateurs qui supportent beforeinstallprompt) --}}
                <button id="pwa-install-btn" onclick="triggerPwaInstall()" class="hidden mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-white text-orange-600 font-bold rounded-xl hover:bg-orange-50 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Installer maintenant
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const DISMISS_KEY = 'pwa_install_dismissed';
    const DISMISS_DAYS = 30;

    // Ne pas afficher si déjà installé (standalone)
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        return;
    }

    // Ne pas afficher si fermé récemment
    const dismissed = localStorage.getItem(DISMISS_KEY);
    if (dismissed) {
        const dismissedAt = parseInt(dismissed, 10);
        if (Date.now() - dismissedAt < DISMISS_DAYS * 24 * 60 * 60 * 1000) {
            return;
        }
    }

    // Détecter l'OS et le navigateur
    const ua = navigator.userAgent || '';
    const isIOS = /iPhone|iPad|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    const isAndroid = /Android/.test(ua);
    const isChrome = /Chrome/.test(ua) && !/Edge|OPR|Brave/.test(ua);

    const card = document.getElementById('pwa-install-card');
    if (!card) return;

    if (isIOS) {
        document.getElementById('pwa-ios').classList.remove('hidden');
    } else if (isAndroid) {
        document.getElementById('pwa-android').classList.remove('hidden');
    } else {
        document.getElementById('pwa-desktop').classList.remove('hidden');
    }

    card.classList.remove('hidden');

    // Intercepter beforeinstallprompt pour le bouton "Installer maintenant"
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        document.getElementById('pwa-install-btn').classList.remove('hidden');
    });

    window.triggerPwaInstall = function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(result) {
                if (result.outcome === 'accepted') {
                    card.classList.add('hidden');
                }
                deferredPrompt = null;
            });
        }
    };

    window.dismissPwaCard = function() {
        localStorage.setItem(DISMISS_KEY, Date.now().toString());
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity = '0';
        card.style.transform = 'translateY(-10px)';
        setTimeout(function() { card.classList.add('hidden'); }, 300);
    };

    // Masquer si l'utilisateur installe l'app
    window.addEventListener('appinstalled', function() {
        card.classList.add('hidden');
    });
})();
</script>
