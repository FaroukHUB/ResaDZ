<x-filament-panels::page>
    {{-- Stats Cards - Row 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        {{-- Commission Due Total --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-danger-100 dark:bg-danger-500/20 flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Commission totale due</p>
                    <p class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                        {{ number_format($totalCommissionDue, 0, ',', ' ') }} DA
                    </p>
                </div>
            </div>
        </div>

        {{-- Commission Paid This Month --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-success-100 dark:bg-success-500/20 flex items-center justify-center">
                    <x-heroicon-o-check-badge class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Percu ce mois</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ number_format($totalCommissionPaid, 0, ',', ' ') }} DA
                    </p>
                </div>
            </div>
        </div>

        {{-- Loueurs Stats --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-info-100 dark:bg-info-500/20 flex items-center justify-center">
                    <x-heroicon-o-building-storefront class="w-6 h-6 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Loueurs</p>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-success-600 dark:text-success-400 font-semibold">{{ $loueursActive }} actifs</span>
                        <span class="text-warning-600 dark:text-warning-400">{{ $loueursInTrial }} essai</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Taxis/Chauffeurs Stats --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-warning-100 dark:bg-warning-500/20 flex items-center justify-center">
                    <x-heroicon-o-truck class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Chauffeurs/Taxis</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                        {{ $taxisActive }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards - Row 2: Detail par type --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Commission Locations (Loueurs) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border-l-4 border-info-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Locations (Loueurs)</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ number_format($totalCommissionDueLoueurs, 0, ',', ' ') }} DA
                    </p>
                    <p class="text-xs text-gray-500">{{ $bookingsThisMonth }} reservations ce mois</p>
                </div>
                <div class="text-right">
                    <span class="text-xs bg-info-100 text-info-700 px-2 py-1 rounded-full">8-6% dégressif</span>
                </div>
            </div>
        </div>

        {{-- Commission Transferts (Chauffeurs) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border-l-4 border-warning-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Transferts (Chauffeurs)</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ number_format($totalCommissionDueTransfers, 0, ',', ' ') }} DA
                    </p>
                    <p class="text-xs text-gray-500">{{ $transfersThisMonth }} courses ce mois</p>
                </div>
                <div class="text-right">
                    <span class="text-xs bg-warning-100 text-warning-700 px-2 py-1 rounded-full">10% fixe</span>
                </div>
            </div>
        </div>

        {{-- Commission Livraisons (Chauffeurs) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Livraisons (Chauffeurs)</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ number_format($totalCommissionDueDeliveries, 0, ',', ' ') }} DA
                    </p>
                    <p class="text-xs text-gray-500">{{ $deliveriesThisMonth }} livraisons ce mois</p>
                </div>
                <div class="text-right">
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full">10% fixe</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Info Card Loueurs --}}
        <div class="bg-info-50 dark:bg-info-500/10 border border-info-200 dark:border-info-500/20 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <x-heroicon-o-building-storefront class="w-5 h-5 text-info-600 dark:text-info-400 mt-0.5" />
                <div>
                    <p class="text-sm font-semibold text-info-800 dark:text-info-200">Commission Loueurs (taux dégressif)</p>
                    <p class="text-xs text-info-600 dark:text-info-400 mt-1">
                        <span class="font-semibold">1-10 jours : 8%</span> &bull; <span class="font-semibold">+10 jours : 6%</span><br>
                        Commission sur le montant HT de la location (hors livraison/options).
                    </p>
                </div>
            </div>
        </div>

        {{-- Info Card Chauffeurs --}}
        <div class="bg-warning-50 dark:bg-warning-500/10 border border-warning-200 dark:border-warning-500/20 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <x-heroicon-o-truck class="w-5 h-5 text-warning-600 dark:text-warning-400 mt-0.5" />
                <div>
                    <p class="text-sm font-semibold text-warning-800 dark:text-warning-200">Commission Chauffeurs (taux fixe)</p>
                    <p class="text-xs text-warning-600 dark:text-warning-400 mt-1">
                        <span class="font-semibold">Transferts : 10%</span> &bull; <span class="font-semibold">Livraisons : 10%</span><br>
                        Commission fixe sur le montant total de chaque course. Le client ne paie rien en plus.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
