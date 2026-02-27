<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" size="lg">
                Enregistrer les paramètres
            </x-filament::button>
        </div>
    </form>

    {{-- Push Notifications Section --}}
    <div class="mt-8 p-6 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700" x-data="{ showHelp: false }">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications Push</h3>
                    <button
                        type="button"
                        @click="showHelp = !showHelp"
                        class="w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors text-xs font-bold"
                        title="Comment activer les notifications ?"
                    >
                        ?
                    </button>
                </div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Recevez des notifications instantanées sur cet appareil lorsqu'un client effectue une réservation.
                    Les notifications fonctionnent même si le navigateur est fermé.
                </p>

                {{-- Help Panel --}}
                <div x-show="showHelp" x-transition x-cloak class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                    <div class="flex items-start gap-2 mb-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100">Comment activer les notifications ?</h4>
                    </div>
                    <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                        Si les notifications sont bloquées, vous devez les autoriser dans les paramètres de votre navigateur :
                    </p>

                    <div class="space-y-3 text-sm">
                        {{-- Google Chrome --}}
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" fill="#4285F4"/>
                                <circle cx="12" cy="12" r="4" fill="white"/>
                                <path d="M12 6V12L16.5 9" fill="#34A853"/>
                                <path d="M12 12L7.5 15L12 18V12" fill="#FBBC05"/>
                                <path d="M12 12L16.5 15L12 6V12" fill="#EA4335"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Google Chrome</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                    1. Cliquez sur l'icône du cadenas (ou i) dans la barre d'adresse<br>
                                    2. Cliquez sur <strong>Paramètres du site</strong><br>
                                    3. Activez <strong>Notifications</strong> sur "Autoriser"
                                </p>
                            </div>
                        </div>

                        {{-- Mozilla Firefox --}}
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" fill="#FF7139"/>
                                <path d="M12 4C7.58 4 4 7.58 4 12s3.58 8 8 8 8-3.58 8-8c0-1.85-.63-3.55-1.69-4.9L12 12V4z" fill="#FF9500"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Mozilla Firefox</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                    1. Cliquez sur l'icône du cadenas dans la barre d'adresse<br>
                                    2. Cliquez sur <strong>Connexion sécurisée</strong><br>
                                    3. Cliquez sur <strong>Plus d'informations</strong> puis <strong>Permissions</strong><br>
                                    4. Activez <strong>Envoyer des notifications</strong>
                                </p>
                            </div>
                        </div>

                        {{-- Microsoft Edge --}}
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" fill="#0078D4"/>
                                <path d="M6 12c0-3.31 2.69-6 6-6s6 2.69 6 6h-6v6c-3.31 0-6-2.69-6-6z" fill="#50E6FF"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Microsoft Edge</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                    1. Cliquez sur l'icône du cadenas dans la barre d'adresse<br>
                                    2. Cliquez sur <strong>Autorisations pour ce site</strong><br>
                                    3. Activez <strong>Notifications</strong> sur "Autoriser"
                                </p>
                            </div>
                        </div>

                        {{-- Safari --}}
                        <div class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" fill="#006CFF"/>
                                <path d="M12 6l1.5 4.5L18 12l-4.5 1.5L12 18l-1.5-4.5L6 12l4.5-1.5z" fill="white"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Safari (Mac)</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                    1. Allez dans <strong>Safari > Réglages > Sites web</strong><br>
                                    2. Cliquez sur <strong>Notifications</strong> dans la barre latérale<br>
                                    3. Trouvez ce site et sélectionnez <strong>Autoriser</strong>
                                </p>
                            </div>
                        </div>

                        {{-- Mobile --}}
                        <div class="flex items-start gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <svg class="w-6 h-6 flex-shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Sur mobile (Android/iOS)</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                                    Pour recevoir des notifications sur mobile, installez l'application en tant que PWA :<br>
                                    <strong>Android</strong> : Menu du navigateur > "Ajouter à l'écran d'accueil"<br>
                                    <strong>iPhone</strong> : Bouton partager > "Sur l'écran d'accueil"
                                </p>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="showHelp = false"
                        class="mt-4 w-full text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium"
                    >
                        Fermer l'aide
                    </button>
                </div>

                <div class="mt-4">
                    <button
                        type="button"
                        id="push-notification-toggle"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span>Chargement...</span>
                    </button>
                </div>
                <p id="push-status" class="mt-2 text-xs text-gray-500 dark:text-gray-400"></p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/push-notifications.js') }}"></script>
</x-filament-panels::page>
