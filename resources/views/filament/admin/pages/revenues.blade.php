<x-filament-panels::page>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Commission Due --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-danger-100 dark:bg-danger-500/20 flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Commission à percevoir</p>
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perçu ce mois</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ number_format($totalCommissionPaid, 0, ',', ' ') }} DA
                    </p>
                </div>
            </div>
        </div>

        {{-- Bookings This Month --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-info-100 dark:bg-info-500/20 flex items-center justify-center">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Réservations (ce mois)</p>
                    <p class="text-2xl font-bold text-info-600 dark:text-info-400">
                        {{ $bookingsThisMonth }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Loueurs Stats --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-warning-100 dark:bg-warning-500/20 flex items-center justify-center">
                    <x-heroicon-o-building-storefront class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Loueurs</p>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-success-600 dark:text-success-400 font-semibold">{{ $loueursActive }} actifs</span>
                        <span class="text-warning-600 dark:text-warning-400">{{ $loueursInTrial }} essai</span>
                        @if($loueursSuspended > 0)
                            <span class="text-danger-600 dark:text-danger-400">{{ $loueursSuspended }} susp.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-primary-50 dark:bg-primary-500/10 border border-primary-200 dark:border-primary-500/20 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <x-heroicon-o-information-circle class="w-5 h-5 text-primary-600 dark:text-primary-400 mt-0.5" />
            <div>
                <p class="text-sm text-primary-800 dark:text-primary-200">
                    <strong>Commission :</strong> taux dégressif selon la durée de location (configurable dans Paramètres plateforme)
                </p>
                <p class="text-xs text-primary-600 dark:text-primary-400 mt-1">
                    <span class="font-semibold">1-3 jours : 8%</span> &bull; <span class="font-semibold">4-7 jours : 6%</span> &bull; <span class="font-semibold">8+ jours : 5%</span><br>
                    Commission prélevée uniquement au loueur, sur le montant HT de la location (hors livraison/options). Le locataire ne paie aucune commission.
                </p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
