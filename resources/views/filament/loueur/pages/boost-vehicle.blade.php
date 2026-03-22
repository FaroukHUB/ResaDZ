<x-filament-panels::page>
    {{-- Welcome Banner --}}
    <div class="welcome-banner mb-6" style="background: linear-gradient(135deg, #f59e0b 0%, #eab308 100%);">
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <x-heroicon-o-rocket-launch class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Booster vos vehicules</h2>
                        <p class="text-white/80 text-sm">Augmentez votre visibilite et obtenez plus de reservations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Guide Section --}}
    <div class="mb-6 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-yellow-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-question-mark-circle class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1">
                <p class="font-semibold text-amber-900 dark:text-amber-100">Comment fonctionne le boost ?</p>
                <ul class="text-sm text-amber-700 dark:text-amber-300 mt-2 space-y-1">
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">1.</span>
                        <span><strong>Choisissez un vehicule</strong> parmi votre flotte active</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">2.</span>
                        <span><strong>Selectionnez une duree</strong> : 3, 7 ou 30 jours selon votre budget</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">3.</span>
                        <span><strong>Effet immediat</strong> : Votre vehicule apparait en tete des resultats avec un badge "Sponsorise"</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">4.</span>
                        <span><strong>+40% de clics en moyenne</strong> grace a la mise en avant prioritaire</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Active Boosts --}}
    @if($this->activeBoosts->count() > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Boosts actifs</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->activeBoosts as $boost)
                    <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-amber-100 dark:bg-amber-800 rounded-lg">
                                <x-heroicon-o-rocket-launch class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $boost->vehicle->brand->name }} {{ $boost->vehicle->model }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $boost->boostPackage->name }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Expire dans</span>
                            <span class="font-semibold text-amber-600 dark:text-amber-400">
                                {{ $boost->remaining_days }} jour(s)
                            </span>
                        </div>
                        <div class="mt-2 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            @php
                                $totalDays = $boost->boostPackage->duration_days;
                                $remaining = $boost->remaining_days;
                                $percentage = ($remaining / $totalDays) * 100;
                            @endphp
                            <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Pending Boosts --}}
    @if($this->pendingBoosts->count() > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Boosts en attente de paiement</h3>
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-xl p-4">
                @foreach($this->pendingBoosts as $boost)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-orange-200 dark:border-orange-700' : '' }}">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-clock class="w-5 h-5 text-orange-500" />
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $boost->vehicle->brand->name }} {{ $boost->vehicle->model }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $boost->boostPackage->name }} - {{ $boost->boostPackage->formatted_price }}
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-800 text-orange-700 dark:text-orange-300 text-sm font-medium rounded-full">
                            En attente
                        </span>
                    </div>
                @endforeach
                <p class="mt-3 text-sm text-orange-700 dark:text-orange-300">
                    <x-heroicon-o-information-circle class="w-4 h-4 inline" />
                    Contactez-nous pour effectuer le paiement et activer vos boosts.
                </p>
            </div>
        </div>
    @endif

    {{-- Boost Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-3 bg-gradient-to-br from-amber-500 to-yellow-400 rounded-xl">
                <x-heroicon-o-rocket-launch class="w-6 h-6 text-white" />
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nouveau boost</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Mettez votre véhicule en avant pour plus de visibilité
                </p>
            </div>
        </div>

        {{-- Benefits --}}
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-green-500 shrink-0 mt-0.5" />
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Plus de visibilité</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Apparaissez en premier dans les recherches</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <x-heroicon-o-star class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" />
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Badge "Sponsorisé"</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Attirez l'attention des visiteurs</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <x-heroicon-o-cursor-arrow-rays class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Plus de clics</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Augmentez vos chances de location</p>
                </div>
            </div>
        </div>

        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" size="lg">
                    <x-heroicon-o-rocket-launch class="w-5 h-5 mr-2" />
                    Acheter le boost
                </x-filament::button>
            </div>
        </form>
    </div>

    {{-- Packages Info --}}
    @if($this->availablePackages->count() > 0)
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Nos packs boost</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($this->availablePackages as $package)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-amber-500 to-yellow-400 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">{{ $package->duration_days }}j</span>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $package->name }}</h4>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 my-2">
                            {{ $package->formatted_price }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $package->description ?? 'Boost de ' . $package->duration_days . ' jours' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-panels::page>
