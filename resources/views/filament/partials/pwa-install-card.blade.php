{{-- Card d'installation PWA --}}
<div id="pwa-install-card" class="hidden" style="margin-bottom: 1.5rem;">
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 1rem; padding: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.08);">

        {{-- Cercles décoratifs --}}
        <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,107,44,0.15);"></div>
        <div style="position: absolute; bottom: -20px; left: -20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(59,130,246,0.1);"></div>

        {{-- Bouton fermer --}}
        <button onclick="dismissPwaCard()" style="position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.1); border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
            <svg width="14" height="14" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Header --}}
        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
            <div style="width: 52px; height: 52px; background: linear-gradient(135deg, #FF6B2C, #f59e0b); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(255,107,44,0.3);">
                <svg width="28" height="28" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div>
                <h3 style="color: #ffffff; font-weight: 700; font-size: 1.1rem; margin: 0; line-height: 1.3;">
                    Telecharger l'app ResaDZ
                </h3>
                <p style="color: #94a3b8; font-size: 0.82rem; margin: 4px 0 0 0; line-height: 1.4;">
                    Ajoutez ResaDZ sur votre ecran d'accueil pour y acceder comme une vraie application
                </p>
            </div>
        </div>

        {{-- Avantages --}}
        <div style="display: flex; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px; background: rgba(34,197,94,0.12); padding: 5px 12px; border-radius: 20px;">
                <svg width="15" height="15" fill="#22c55e" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                <span style="color: #4ade80; font-size: 0.78rem; font-weight: 600;">Acces rapide</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px; background: rgba(59,130,246,0.12); padding: 5px 12px; border-radius: 20px;">
                <svg width="15" height="15" fill="#3b82f6" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                <span style="color: #60a5fa; font-size: 0.78rem; font-weight: 600;">Notifications</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px; background: rgba(168,85,247,0.12); padding: 5px 12px; border-radius: 20px;">
                <svg width="15" height="15" fill="#a855f7" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd"/></svg>
                <span style="color: #c084fc; font-size: 0.78rem; font-weight: 600;">Mode hors-ligne</span>
            </div>
        </div>

        {{-- Instructions (une seule visible) --}}

        {{-- Chrome Android --}}
        <div id="pwa-android" class="hidden">
            <div style="background: rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#34a853"><path d="M17.523 15.341a1 1 0 0 0-.737-.16 1 1 0 0 0-.618.434c-.102.165-.14.362-.107.554a1 1 0 0 0 .444.667c.164.103.361.14.554.107a1 1 0 0 0 .667-.444 1 1 0 0 0-.203-1.158zM6.863 15.341a1 1 0 0 0-.203 1.158 1 1 0 0 0 .667.444c.193.033.39-.004.554-.107a1 1 0 0 0 .444-.667 1 1 0 0 0-.107-.554 1 1 0 0 0-.618-.434 1 1 0 0 0-.737.16zM17.785 8.563l1.924-3.332a.4.4 0 0 0-.146-.546.4.4 0 0 0-.546.146l-1.948 3.374A11.2 11.2 0 0 0 12.193 7a11.2 11.2 0 0 0-4.876 1.205L5.369 4.83a.4.4 0 0 0-.546-.146.4.4 0 0 0-.146.546l1.924 3.332C3.601 10.267 1.543 13.389 1.2 17h21.986c-.343-3.611-2.401-6.733-5.401-8.437z"/></svg>
                    <span style="color: #e2e8f0; font-weight: 700; font-size: 0.85rem;">Chrome sur Android</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #FF6B2C; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Appuyez sur le menu <strong style="color: #fff;">&#8942;</strong> en haut a droite</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #FF6B2C; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Selectionnez <strong style="color: #fff;">"Ajouter a l'ecran d'accueil"</strong></span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #22c55e; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Appuyez sur <strong style="color: #fff;">"Ajouter"</strong> <span style="color: #22c55e;">&#10003;</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Safari iOS --}}
        <div id="pwa-ios-safari" class="hidden">
            <div style="background: rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#0ea5e9"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm3.07 5.93l-2.82 6.36-6.36 2.82 2.82-6.36 6.36-2.82zM12 13a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    <span style="color: #e2e8f0; font-weight: 700; font-size: 0.85rem;">Safari sur iPhone / iPad</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #0ea5e9; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Appuyez sur le bouton <strong style="color: #fff;">Partager</strong> <span style="color: #0ea5e9;">&#9757;</span> en bas de l'ecran</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #0ea5e9; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Selectionnez <strong style="color: #fff;">"Sur l'ecran d'accueil"</strong></span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #22c55e; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Appuyez sur <strong style="color: #fff;">"Ajouter"</strong> <span style="color: #22c55e;">&#10003;</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chrome iOS (ouvre Safari pour l'install) --}}
        <div id="pwa-ios-chrome" class="hidden">
            <div style="background: rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <svg width="18" height="18" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#4285f4"/><circle cx="12" cy="12" r="4.5" fill="#fff"/><path d="M12 7.5h9.5c-.3-1.4-.9-2.7-1.8-3.8L14 12l-2-4.5z" fill="#ea4335"/><path d="M7.5 16.3L3 8.5c-.6 1.1-1 2.3-1 3.5 0 3.5 1.8 6.5 4.5 8.3l3-5.5-2-1.5z" fill="#34a853"/><path d="M16.5 16.3l-3 5.5c1 .2 1.7.2 2.5.2 3.5 0 6.5-1.8 8-4.5l-5.5-3-2 1.8z" fill="#fbbc05"/></svg>
                    <span style="color: #e2e8f0; font-weight: 700; font-size: 0.85rem;">Chrome sur iPhone / iPad</span>
                </div>
                <div style="background: rgba(251,191,36,0.1); border: 1px solid rgba(251,191,36,0.2); border-radius: 8px; padding: 10px 12px; margin-bottom: 12px;">
                    <p style="color: #fbbf24; font-size: 0.8rem; margin: 0; line-height: 1.4;">
                        <strong>&#9888;</strong> Sur iPhone, l'installation se fait uniquement via <strong>Safari</strong>. Ouvrez ce lien dans Safari :
                    </p>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #f59e0b; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Ouvrez <strong style="color: #fff;">Safari</strong> et allez sur <strong style="color: #FF6B2C;">resadz.com</strong></span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #0ea5e9; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Appuyez sur <strong style="color: #fff;">Partager</strong> <span style="color: #0ea5e9;">&#9757;</span> puis <strong style="color: #fff;">"Sur l'ecran d'accueil"</strong></span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #22c55e; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Appuyez sur <strong style="color: #fff;">"Ajouter"</strong> <span style="color: #22c55e;">&#10003;</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chrome Desktop --}}
        <div id="pwa-desktop" class="hidden">
            <div style="background: rgba(255,255,255,0.06); border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" stroke="#a78bfa" stroke-width="1.8"/><path d="M8 21h8M12 17v4" stroke="#a78bfa" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span style="color: #e2e8f0; font-weight: 700; font-size: 0.85rem;">Chrome sur PC / Mac</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #a855f7; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Cliquez sur l'icone <strong style="color: #fff;">&#8853;</strong> dans la barre d'adresse a droite</span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span style="background: #22c55e; color: #fff; font-weight: 700; font-size: 0.75rem; min-width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</span>
                        <span style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.4;">Cliquez sur <strong style="color: #fff;">"Installer"</strong> <span style="color: #22c55e;">&#10003;</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bouton installer natif (beforeinstallprompt) --}}
        <button id="pwa-install-btn" onclick="triggerPwaInstall()" class="hidden" style="margin-top: 16px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #FF6B2C, #f59e0b); color: #fff; font-weight: 700; font-size: 0.95rem; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(255,107,44,0.35); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(255,107,44,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(255,107,44,0.35)'">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Installer ResaDZ maintenant
        </button>
    </div>
</div>

<script>
(function() {
    var DISMISS_KEY = 'pwa_install_dismissed';
    var DISMISS_DAYS = 30;

    // Ne pas afficher si deja installe (standalone)
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        return;
    }

    // Ne pas afficher si ferme recemment
    var dismissed = localStorage.getItem(DISMISS_KEY);
    if (dismissed) {
        var dismissedAt = parseInt(dismissed, 10);
        if (Date.now() - dismissedAt < DISMISS_DAYS * 24 * 60 * 60 * 1000) {
            return;
        }
    }

    // Detecter OS et navigateur
    var ua = navigator.userAgent || '';
    var isIOS = /iPhone|iPad|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var isAndroid = /Android/.test(ua);
    var isChromeOnIOS = isIOS && /CriOS/.test(ua);
    var isSafari = isIOS && /Safari/.test(ua) && !(/CriOS|FxiOS|OPiOS|EdgiOS/.test(ua));

    var card = document.getElementById('pwa-install-card');
    if (!card) return;

    if (isChromeOnIOS) {
        // Chrome sur iOS => doit passer par Safari
        document.getElementById('pwa-ios-chrome').classList.remove('hidden');
    } else if (isIOS && isSafari) {
        // Safari natif sur iOS
        document.getElementById('pwa-ios-safari').classList.remove('hidden');
    } else if (isIOS) {
        // Autre navigateur iOS => meme chose que Chrome iOS
        document.getElementById('pwa-ios-chrome').classList.remove('hidden');
    } else if (isAndroid) {
        document.getElementById('pwa-android').classList.remove('hidden');
    } else {
        document.getElementById('pwa-desktop').classList.remove('hidden');
    }

    card.classList.remove('hidden');

    // Intercepter beforeinstallprompt pour le bouton natif
    var deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        var btn = document.getElementById('pwa-install-btn');
        if (btn) btn.classList.remove('hidden');
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
