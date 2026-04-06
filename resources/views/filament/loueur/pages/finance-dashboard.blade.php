<x-filament-panels::page>
    <div class="space-y-6">
        @if($isTaxi ?? false)
            {{-- ========== TAXI/CHAUFFEUR DASHBOARD ========== --}}

            {{-- Welcome Banner with Guide --}}
            <div class="ch-hero" style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); box-shadow: 0 4px 20px rgba(99,102,241,0.25);">
                <div class="relative z-10 w-full">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <x-heroicon-o-chart-bar-square class="w-7 h-7 text-white" />
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Tableau de bord financier 💹</h2>
                                <p class="text-white/80 text-sm mt-1">Suivez vos revenus et depenses en temps reel</p>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl transition text-sm font-medium">
                                    <x-heroicon-o-question-mark-circle class="w-5 h-5" />
                                    Guide
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-4 text-gray-700 dark:text-gray-300 text-sm z-50">
                                    <h4 class="font-bold text-gray-900 dark:text-white mb-2">Comment lire ce tableau ?</h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start gap-2">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mt-1.5"></span>
                                            <span><strong>CA (Chiffre d'affaires)</strong> : Total des revenus generes par vos courses</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mt-1.5"></span>
                                            <span><strong>Depenses</strong> : Carburant, entretien, et autres frais enregistres</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-1.5"></span>
                                            <span><strong>Solde</strong> : Benefice net = CA - Depenses</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="w-2 h-2 bg-amber-500 rounded-full mt-1.5"></span>
                                            <span><strong>Commission</strong> : 10% a reverser a ResaDZ sur les courses confirmees</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Tips --}}
            <div style="background: linear-gradient(90deg, #EEF2FF 0%, #F5F3FF 100%); border: 1px solid #C7D2FE; border-radius: 1rem; padding: 1rem;">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 rounded-xl" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                        <x-heroicon-o-light-bulb class="w-5 h-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="font-bold" style="color: #312E81;">Astuce du jour</p>
                        <p class="text-sm mt-1" style="color: #4338CA;">
                            Enregistrez vos depenses regulierement (carburant, peages, entretien) dans l'onglet <strong>Transactions</strong> pour avoir une vision precise de votre rentabilite. Un suivi quotidien vous aide a optimiser vos gains !
                        </p>
                    </div>
                </div>
            </div>

            {{-- Period Stats with CA breakdown --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Today --}}
                <div class="ch-stat-card group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1, #8B5CF6);">
                            <x-heroicon-o-sun class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Aujourd'hui</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">CA Transferts</span>
                            <span class="text-sm font-bold text-blue-600">{{ number_format($todayTransfers ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">CA Livraisons</span>
                            <span class="text-sm font-bold text-amber-600">{{ number_format($todayDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Total CA</span>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($todayIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Depenses</span>
                            <span class="text-lg font-bold text-red-600">-{{ number_format($todayExpenses ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center bg-gradient-to-r {{ ($todayNet ?? 0) >= 0 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} p-3 rounded-xl">
                                <span class="font-semibold text-white">Solde</span>
                                <span class="text-xl font-black text-white">
                                    {{ ($todayNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($todayNet ?? 0, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- This Week --}}
                <div class="stat-card group hover:shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-container icon-container-md bg-gradient-to-br from-cyan-500 to-blue-600">
                            <x-heroicon-o-calendar class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Cette semaine</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">CA Transferts</span>
                            <span class="text-sm font-bold text-blue-600">{{ number_format($weekTransfers ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">CA Livraisons</span>
                            <span class="text-sm font-bold text-amber-600">{{ number_format($weekDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Total CA</span>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($weekIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Depenses</span>
                            <span class="text-lg font-bold text-red-600">-{{ number_format($weekExpenses ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center bg-gradient-to-r {{ ($weekNet ?? 0) >= 0 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} p-3 rounded-xl">
                                <span class="font-semibold text-white">Solde</span>
                                <span class="text-xl font-black text-white">
                                    {{ ($weekNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($weekNet ?? 0, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- This Month --}}
                <div class="stat-card group hover:shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-container icon-container-md bg-gradient-to-br from-violet-500 to-purple-600">
                            <x-heroicon-o-calendar-days class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Ce mois</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">CA Transferts</span>
                            <span class="text-sm font-bold text-blue-600">{{ number_format($monthTransfers ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">CA Livraisons</span>
                            <span class="text-sm font-bold text-amber-600">{{ number_format($monthDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Total CA</span>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($monthIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Depenses</span>
                            <span class="text-lg font-bold text-red-600">-{{ number_format($monthExpenses ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center bg-gradient-to-r {{ ($monthNet ?? 0) >= 0 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} p-3 rounded-xl">
                                <span class="font-semibold text-white">Solde</span>
                                <span class="text-xl font-black text-white">
                                    {{ ($monthNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($monthNet ?? 0, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Commission & Pending Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Commission Due --}}
                @if(($totalCommissionDue ?? 0) > 0)
                <div class="stat-card border-l-4 border-red-500 bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-gray-800">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/30">
                            <x-heroicon-o-receipt-percent class="w-7 h-7 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-red-800 dark:text-red-200 font-semibold">Commission due (10%)</p>
                            <p class="text-3xl font-black text-red-600 dark:text-red-400">{{ number_format($totalCommissionDue ?? 0, 0, ',', ' ') }} DA</p>
                            <div class="mt-2 flex gap-4 text-xs">
                                <span class="badge-modern badge-info">Transferts: {{ number_format($commissionTransfers ?? 0, 0, ',', ' ') }} DA</span>
                                <span class="badge-modern badge-warning">Livraisons: {{ number_format($commissionDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Pending Income --}}
                @if(($pendingIncome ?? 0) > 0)
                <div class="stat-card border-l-4 border-amber-500 bg-gradient-to-r from-amber-50 to-white dark:from-amber-900/20 dark:to-gray-800">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 animate-pulse">
                            <x-heroicon-o-clock class="w-7 h-7 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-amber-800 dark:text-amber-200 font-semibold">Courses en attente</p>
                            <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ number_format($pendingIncome ?? 0, 0, ',', ' ') }} DA</p>
                            <div class="mt-2 flex gap-4 text-xs">
                                <span class="badge-modern badge-info">Transferts: {{ number_format($pendingTransfersIncome ?? 0, 0, ',', ' ') }} DA</span>
                                <span class="badge-modern badge-warning">Livraisons: {{ number_format($pendingDeliveriesIncome ?? 0, 0, ',', ' ') }} DA</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Courses Counter --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="stat-card group overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-transparent rounded-bl-full"></div>
                    <div class="relative flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                            <x-heroicon-o-map-pin class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <p class="text-sm text-blue-800 dark:text-blue-200 font-semibold">Transferts ce mois</p>
                            <p class="text-4xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ $monthTransfersCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="stat-card group overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-transparent rounded-bl-full"></div>
                    <div class="relative flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                            <x-heroicon-o-inbox-stack class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <p class="text-sm text-amber-800 dark:text-amber-200 font-semibold">Livraisons ce mois</p>
                            <p class="text-4xl font-black bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">{{ $monthDeliveriesCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Courses (Transfers + Deliveries) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Recent Transfers --}}
                <div class="card-modern overflow-hidden p-0">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5" />
                            Derniers transferts
                        </h3>
                        <a href="{{ route('filament.loueur.resources.transfer-bookings.index') }}" class="text-sm text-white/80 hover:text-white font-medium flex items-center gap-1">
                            Voir tout
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentTransfers ?? [] as $transfer)
                            <div class="data-row px-6 py-4">
                                <div class="flex items-center justify-between w-full">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $transfer->reference }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <x-heroicon-o-map-pin class="w-3 h-3" />
                                            {{ $transfer->pickup_location }} &rarr; {{ $transfer->dropoff_location }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $transfer->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-green-600">{{ number_format($transfer->price ?? 0, 0, ',', ' ') }} DA</span>
                                        <span class="badge-modern block mt-1
                                            {{ $transfer->status === 'completed' ? 'badge-success' :
                                               ($transfer->status === 'confirmed' ? 'badge-info' :
                                               ($transfer->status === 'pending' ? 'badge-warning' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ $transfer->status === 'completed' ? 'Termine' :
                                               ($transfer->status === 'confirmed' ? 'Confirme' :
                                               ($transfer->status === 'pending' ? 'En attente' : $transfer->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-12">
                                <div class="empty-state-icon bg-blue-100 dark:bg-blue-900/30">
                                    <x-heroicon-o-arrow-path-rounded-square class="w-6 h-6 text-blue-500" />
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Aucun transfert recent</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Deliveries --}}
                <div class="card-modern overflow-hidden p-0">
                    <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-600 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-truck class="w-5 h-5" />
                            Dernieres livraisons
                        </h3>
                        <a href="{{ route('filament.loueur.resources.delivery-bookings.index') }}" class="text-sm text-white/80 hover:text-white font-medium flex items-center gap-1">
                            Voir tout
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentDeliveries ?? [] as $delivery)
                            <div class="data-row px-6 py-4">
                                <div class="flex items-center justify-between w-full">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $delivery->reference }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <x-heroicon-o-cube class="w-3 h-3" />
                                            {{ $delivery->pickup_city }} &rarr; {{ $delivery->delivery_city }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-green-600">{{ number_format($delivery->price ?? 0, 0, ',', ' ') }} DA</span>
                                        <span class="badge-modern block mt-1
                                            {{ $delivery->status === 'delivered' ? 'badge-success' :
                                               ($delivery->status === 'in_transit' ? 'bg-purple-100 text-purple-800' :
                                               ($delivery->status === 'picked_up' ? 'badge-info' :
                                               ($delivery->status === 'confirmed' ? 'badge-info' :
                                               ($delivery->status === 'pending' ? 'badge-warning' : 'bg-gray-100 text-gray-800')))) }}">
                                            {{ $delivery->status === 'delivered' ? 'Livre' :
                                               ($delivery->status === 'in_transit' ? 'En transit' :
                                               ($delivery->status === 'picked_up' ? 'Recupere' :
                                               ($delivery->status === 'confirmed' ? 'Confirme' :
                                               ($delivery->status === 'pending' ? 'En attente' : $delivery->status)))) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-12">
                                <div class="empty-state-icon bg-amber-100 dark:bg-amber-900/30">
                                    <x-heroicon-o-truck class="w-6 h-6 text-amber-500" />
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Aucune livraison recente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="card-modern overflow-hidden p-0">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                        Dernieres transactions
                    </h3>
                    <a href="{{ route('filament.loueur.resources.transactions.index') }}" class="text-sm text-white/80 hover:text-white font-medium flex items-center gap-1">
                        Voir tout
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentTransactions ?? [] as $transaction)
                        <div class="data-row px-6 py-4">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $transaction->type === 'income' ? 'bg-gradient-to-br from-green-400 to-emerald-500' : 'bg-gradient-to-br from-red-400 to-rose-500' }}">
                                        @if($transaction->type === 'income')
                                            <x-heroicon-o-arrow-down class="w-6 h-6 text-white" />
                                        @else
                                            <x-heroicon-o-arrow-up class="w-6 h-6 text-white" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaction->transaction_date->format('d/m/Y') }}
                                            @if($transaction->expenseCategory)
                                                <span class="mx-1">&bull;</span>
                                                <span class="text-gray-400">{{ $transaction->expenseCategory->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xl font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->getFormattedAmount() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state py-12">
                            <div class="empty-state-icon bg-emerald-100 dark:bg-emerald-900/30">
                                <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-500" />
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Aucune transaction pour le moment</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @else
            {{-- ========== LOUEUR (RENTAL) DASHBOARD ========== --}}

            {{-- Welcome Banner --}}
            <div class="hero-card" style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%);">
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <x-heroicon-o-chart-bar-square class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Tableau de bord financier</h2>
                            <p class="text-white/80 text-sm mt-1">Gerez vos finances en toute simplicite</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Period Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Today --}}
                <div class="stat-card stat-card-orange group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-container icon-container-md bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B]">
                            <x-heroicon-o-sun class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Aujourd'hui</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Entrees</span>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($todayIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Sorties</span>
                            <span class="text-lg font-bold text-red-600">-{{ number_format($todayExpenses ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center bg-gradient-to-r {{ ($todayNet ?? 0) >= 0 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} p-3 rounded-xl">
                                <span class="font-semibold text-white">Solde</span>
                                <span class="text-xl font-black text-white">
                                    {{ ($todayNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($todayNet ?? 0, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- This Week --}}
                <div class="stat-card group hover:shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-container icon-container-md bg-gradient-to-br from-cyan-500 to-blue-600">
                            <x-heroicon-o-calendar class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Cette semaine</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Entrees</span>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($weekIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Sorties</span>
                            <span class="text-lg font-bold text-red-600">-{{ number_format($weekExpenses ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center bg-gradient-to-r {{ ($weekNet ?? 0) >= 0 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} p-3 rounded-xl">
                                <span class="font-semibold text-white">Solde</span>
                                <span class="text-xl font-black text-white">
                                    {{ ($weekNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($weekNet ?? 0, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- This Month --}}
                <div class="stat-card group hover:shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="icon-container icon-container-md bg-gradient-to-br from-violet-500 to-purple-600">
                            <x-heroicon-o-calendar-days class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Ce mois</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Entrees</span>
                            <span class="text-lg font-bold text-green-600">+{{ number_format($monthIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <span class="text-gray-600 dark:text-gray-300 text-sm">Sorties</span>
                            <span class="text-lg font-bold text-red-600">-{{ number_format($monthExpenses ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                            <div class="flex justify-between items-center bg-gradient-to-r {{ ($monthNet ?? 0) >= 0 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-rose-600' }} p-3 rounded-xl">
                                <span class="font-semibold text-white">Solde</span>
                                <span class="text-xl font-black text-white">
                                    {{ ($monthNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($monthNet ?? 0, 0, ',', ' ') }} DA
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Income --}}
            @if(($pendingIncome ?? 0) > 0)
            <div class="stat-card border-l-4 border-amber-500 bg-gradient-to-r from-amber-50 to-white dark:from-amber-900/20 dark:to-gray-800">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 animate-pulse">
                        <x-heroicon-o-clock class="w-7 h-7 text-white" />
                    </div>
                    <div>
                        <p class="text-sm text-amber-800 dark:text-amber-200 font-semibold">Revenus en attente (reservations confirmees/en cours)</p>
                        <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ number_format($pendingIncome ?? 0, 0, ',', ' ') }} DA</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Transactions --}}
            <div class="card-modern overflow-hidden p-0">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                        Dernieres transactions
                    </h3>
                    <a href="{{ route('filament.loueur.resources.transactions.index') }}" class="text-sm text-white/80 hover:text-white font-medium flex items-center gap-1">
                        Voir tout
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentTransactions ?? [] as $transaction)
                        <div class="data-row px-6 py-4">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $transaction->type === 'income' ? 'bg-gradient-to-br from-green-400 to-emerald-500' : 'bg-gradient-to-br from-red-400 to-rose-500' }}">
                                        @if($transaction->type === 'income')
                                            <x-heroicon-o-arrow-down class="w-6 h-6 text-white" />
                                        @else
                                            <x-heroicon-o-arrow-up class="w-6 h-6 text-white" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaction->transaction_date->format('d/m/Y') }}
                                            @if($transaction->expenseCategory)
                                                <span class="mx-1">&bull;</span>
                                                <span class="text-gray-400">{{ $transaction->expenseCategory->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xl font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->getFormattedAmount() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state py-12">
                            <div class="empty-state-icon bg-emerald-100 dark:bg-emerald-900/30">
                                <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-500" />
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Aucune transaction pour le moment</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

</x-filament-panels::page>
