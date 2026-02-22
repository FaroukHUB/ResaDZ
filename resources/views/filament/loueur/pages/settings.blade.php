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
    <div class="mt-8 p-6 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications Push</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Recevez des notifications instantanées sur cet appareil lorsqu'un client effectue une réservation.
                    Les notifications fonctionnent même si le navigateur est fermé.
                </p>
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
