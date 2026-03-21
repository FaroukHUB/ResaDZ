<x-filament-panels::page>
    {{-- Welcome Banner --}}
    <div class="welcome-banner mb-6" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold">Parametres</h2>
                    <p class="text-white/80 text-sm">Personnalisez votre espace et vos preferences</p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save">
        <div class="card-modern mb-6">
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit" size="lg" class="btn-gradient-primary">
                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                Enregistrer les parametres
            </x-filament::button>
        </div>
    </form>

    {{-- Push Notifications Section --}}
    <div class="mt-8 card-modern" x-data="{ showHelp: false }">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30">
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
                        class="w-6 h-6 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white hover:scale-110 transition-transform text-xs font-bold shadow"
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
                <div x-show="showHelp" x-transition x-cloak class="mt-4 card-modern bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-blue-900 dark:text-blue-100">Comment activer les notifications ?</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                Si les notifications sont bloquees, vous devez les autoriser dans les parametres de votre navigateur.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        {{-- Google Chrome --}}
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow">
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
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow">
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
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow">
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
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow">
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
                        <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
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
                                    <p class="text-xs"><strong class="text-amber-700 dark:text-amber-300">Android</strong> : Menu du navigateur > "Ajouter a l'ecran d'accueil"</p>
                                    <p class="text-xs"><strong class="text-amber-700 dark:text-amber-300">iPhone</strong> : Bouton partager > "Sur l'ecran d'accueil"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="showHelp = false"
                        class="mt-4 w-full btn-modern btn-gradient-primary text-sm"
                    >
                        Fermer l'aide
                    </button>
                </div>

                <div class="mt-6">
                    <button
                        type="button"
                        id="push-notification-toggle"
                        class="btn-modern bg-gradient-to-r from-amber-500 to-orange-600 text-white hover:shadow-lg hover:shadow-amber-500/30"
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
