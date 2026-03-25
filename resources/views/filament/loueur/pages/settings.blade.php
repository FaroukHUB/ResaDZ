<x-filament-panels::page>
    {{-- Welcome Banner --}}
    <div class="welcome-banner mb-6 rounded-2xl px-6 py-5 md:px-8 md:py-6 relative overflow-hidden" style="background: linear-gradient(135deg, #6366F1 0%, #F59E0B 100%); box-shadow: 0 8px 32px rgba(99,102,241,0.25);">
        <div class="absolute inset-0 opacity-10">
            <svg class="absolute -right-6 -top-6 w-40 h-40 text-white/20" fill="currentColor" viewBox="0 0 24 24"><path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="icon-container w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center" style="box-shadow: 0 2px 12px rgba(0,0,0,0.1);">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Parametres</h2>
                    <p class="text-white/85 text-sm">Personnalisez votre espace et vos preferences</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Guide Section --}}
    <div class="mb-6 rounded-2xl p-5 animate-slide-up" style="background: linear-gradient(135deg, #FFF7ED 0%, #FFFBEB 100%); border: 1px solid rgba(99,102,241,0.15); box-shadow: 0 4px 24px rgba(99,102,241,0.06);">
        <div class="flex items-start gap-3">
            <div class="icon-container w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 4px 12px rgba(99,102,241,0.3);">
                <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1">
                <p class="font-bold" style="color: #C2410C;">Guide des parametres</p>
                <div class="text-sm mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3" style="color: #9A3412;">
                    <div class="flex items-start gap-2 p-2.5 bg-white/70 dark:bg-gray-800/50 rounded-xl" style="transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-building-storefront class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="dark:text-gray-300"><strong class="dark:text-white">Profil</strong> : Nom, description et localisation de votre agence</span>
                    </div>
                    <div class="flex items-start gap-2 p-2.5 bg-white/70 dark:bg-gray-800/50 rounded-xl" style="transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-phone class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="dark:text-gray-300"><strong class="dark:text-white">Contact</strong> : Telephone, WhatsApp et reseaux sociaux</span>
                    </div>
                    <div class="flex items-start gap-2 p-2.5 bg-white/70 dark:bg-gray-800/50 rounded-xl" style="transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-banknotes class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="dark:text-gray-300"><strong class="dark:text-white">Paiements</strong> : Methodes de paiement acceptees (CIB, PayPal...)</span>
                    </div>
                    <div class="flex items-start gap-2 p-2.5 bg-white/70 dark:bg-gray-800/50 rounded-xl" style="transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-bell class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="dark:text-gray-300"><strong class="dark:text-white">Notifications</strong> : Alertes push, email et WhatsApp</span>
                    </div>
                    <div class="flex items-start gap-2 p-2.5 bg-white/70 dark:bg-gray-800/50 rounded-xl" style="transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-truck class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="dark:text-gray-300"><strong class="dark:text-white">Services</strong> : Activer le transfert ou la livraison</span>
                    </div>
                    <div class="flex items-start gap-2 p-2.5 bg-white/70 dark:bg-gray-800/50 rounded-xl" style="transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-lock-closed class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="dark:text-gray-300"><strong class="dark:text-white">Securite</strong> : Modifier votre mot de passe de connexion</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save">
        <div class="card-modern mb-6" style="box-shadow: 0 4px 24px rgba(99,102,241,0.08);">
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit" size="lg" class="btn-gradient-primary" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); border: none; box-shadow: 0 4px 14px rgba(99,102,241,0.3); border-radius: 0.75rem; transition: all 0.2s ease; padding: 0.75rem 2rem;">
                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                Enregistrer les parametres
            </x-filament::button>
        </div>
    </form>

    {{-- Push Notifications Section --}}
    <div class="mt-8 card-modern" x-data="{ showHelp: false }" style="box-shadow: 0 4px 24px rgba(99,102,241,0.08);">
        <div class="flex items-start gap-4">
            <div class="icon-container w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 6px 20px rgba(99,102,241,0.3);">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Notifications Push</h3>
                    <button
                        type="button"
                        @click="showHelp = !showHelp"
                        class="w-6 h-6 flex items-center justify-center rounded-full text-white hover:scale-110 transition-transform text-xs font-bold" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 2px 8px rgba(99,102,241,0.3);"
                        title="Comment activer les notifications ?"
                    >
                        ?
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Recevez des notifications instantanees sur cet appareil lorsqu'un client effectue une reservation.
                    Les notifications fonctionnent meme si le navigateur est ferme.
                </p>

                {{-- Help Panel --}}
                <div x-show="showHelp" x-transition x-cloak class="mt-4 card-modern border" style="background: linear-gradient(135deg, #FFF7ED, #FFFBEB); border-color: rgba(99,102,241,0.2); box-shadow: 0 4px 24px rgba(99,102,241,0.08);">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="icon-container w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold" style="color: #C2410C;">Comment activer les notifications ?</h4>
                            <p class="text-sm mt-1" style="color: #9A3412;">
                                Si les notifications sont bloquees, vous devez les autoriser dans les parametres de votre navigateur.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        {{-- Google Chrome --}}
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">
                            <div class="w-10 h-10 bg-gradient-to-br from-red-500 via-yellow-500 to-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Google Chrome</p>
                                <ol class="text-gray-600 dark:text-gray-400 text-xs mt-2 list-decimal list-inside space-y-1">
                                    <li>Cliquez sur l'icone du cadenas dans la barre d'adresse</li>
                                    <li>Cliquez sur <strong class="text-gray-900 dark:text-white">Parametres du site</strong></li>
                                    <li>Activez <strong class="text-gray-900 dark:text-white">Notifications</strong> sur "Autoriser"</li>
                                </ol>
                            </div>
                        </div>

                        {{-- Mozilla Firefox --}}
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Mozilla Firefox</p>
                                <ol class="text-gray-600 dark:text-gray-400 text-xs mt-2 list-decimal list-inside space-y-1">
                                    <li>Cliquez sur l'icone du cadenas dans la barre d'adresse</li>
                                    <li>Cliquez sur <strong class="text-gray-900 dark:text-white">Connexion securisee</strong></li>
                                    <li>Cliquez sur <strong class="text-gray-900 dark:text-white">Plus d'informations</strong> puis <strong class="text-gray-900 dark:text-white">Permissions</strong></li>
                                    <li>Activez <strong class="text-gray-900 dark:text-white">Envoyer des notifications</strong></li>
                                </ol>
                            </div>
                        </div>

                        {{-- Microsoft Edge --}}
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Microsoft Edge</p>
                                <ol class="text-gray-600 dark:text-gray-400 text-xs mt-2 list-decimal list-inside space-y-1">
                                    <li>Cliquez sur l'icone du cadenas dans la barre d'adresse</li>
                                    <li>Cliquez sur <strong class="text-gray-900 dark:text-white">Autorisations pour ce site</strong></li>
                                    <li>Activez <strong class="text-gray-900 dark:text-white">Notifications</strong> sur "Autoriser"</li>
                                </ol>
                            </div>
                        </div>

                        {{-- Safari --}}
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 16px rgba(99,102,241,0.1)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 6l1.5 4.5L18 12l-4.5 1.5L12 18l-1.5-4.5L6 12l4.5-1.5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Safari (Mac)</p>
                                <ol class="text-gray-600 dark:text-gray-400 text-xs mt-2 list-decimal list-inside space-y-1">
                                    <li>Allez dans <strong class="text-gray-900 dark:text-white">Safari > Reglages > Sites web</strong></li>
                                    <li>Cliquez sur <strong class="text-gray-900 dark:text-white">Notifications</strong> dans la barre laterale</li>
                                    <li>Trouvez ce site et selectionnez <strong class="text-gray-900 dark:text-white">Autoriser</strong></li>
                                </ol>
                            </div>
                        </div>

                        {{-- Mobile --}}
                        <div class="flex items-start gap-3 p-4 rounded-xl" style="background: linear-gradient(135deg, #FFF7ED, #FFFBEB); border: 1px solid rgba(99,102,241,0.2);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Sur mobile (Android/iOS)</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-2">
                                    Pour recevoir des notifications sur mobile, installez l'application en tant que PWA :
                                </p>
                                <div class="mt-2 space-y-1">
                                    <p class="text-xs"><strong style="color: #C2410C;">Android</strong> : Menu du navigateur > "Ajouter a l'ecran d'accueil"</p>
                                    <p class="text-xs"><strong style="color: #C2410C;">iPhone</strong> : Bouton partager > "Sur l'ecran d'accueil"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="showHelp = false"
                        class="mt-4 w-full btn-modern text-sm text-white font-semibold py-2.5 rounded-xl" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 4px 14px rgba(99,102,241,0.3); transition: all 0.2s ease;"
                    >
                        Fermer l'aide
                    </button>
                </div>

                <div class="mt-6">
                    <button
                        type="button"
                        id="push-notification-toggle"
                        class="btn-modern text-white font-semibold py-2.5 px-5 rounded-xl inline-flex items-center" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 4px 14px rgba(99,102,241,0.3); transition: all 0.2s ease;"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span>Chargement...</span>
                    </button>
                </div>
                <p id="push-status" class="mt-3 text-xs text-gray-500 dark:text-gray-400"></p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/push-notifications.js') }}"></script>
</x-filament-panels::page>
