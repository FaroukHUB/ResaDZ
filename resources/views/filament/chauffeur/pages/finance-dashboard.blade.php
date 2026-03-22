<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Banner with Guide --}}
        <div class="hero-card" style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Tableau de bord chauffeur</h2>
                            <p class="text-white/80 text-sm mt-1">Suivez vos courses et revenus en temps reel</p>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl transition-all text-sm font-semibold">
                                <x-heroicon-o-question-mark-circle class="w-5 h-5" />
                                Aide
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-5 text-[#1E293B] dark:text-gray-300 text-sm z-50 border border-gray-100 dark:border-gray-700">
                                <h4 class="font-bold text-[#1E293B] dark:text-white mb-3">Comprendre votre tableau de bord</h4>
                                <ul class="space-y-2.5">
                                    <li class="flex items-start gap-2">
                                        <span class="w-2.5 h-2.5 bg-[#6366F1] rounded-full mt-1.5 flex-shrink-0"></span>
                                        <span><strong>Transferts</strong> : Courses aeroport/gare (passagers)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-2.5 h-2.5 bg-[#F59E0B] rounded-full mt-1.5 flex-shrink-0"></span>
                                        <span><strong>Livraisons</strong> : Transport de colis et marchandises</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-2.5 h-2.5 bg-[#10B981] rounded-full mt-1.5 flex-shrink-0"></span>
                                        <span><strong>Solde</strong> : Votre benefice apres depenses</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="w-2.5 h-2.5 bg-[#EF4444] rounded-full mt-1.5 flex-shrink-0"></span>
                                        <span><strong>Commission</strong> : 10% a payer a ResaDZ</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Tips --}}
        <div class="stat-card bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/10 dark:to-amber-900/10 border border-orange-200 dark:border-orange-800">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B] rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-[#1E293B] dark:text-white">Conseil du jour</p>
                    <p class="text-sm text-[#64748B] mt-1">
                        Pensez a mettre a jour vos <strong>disponibilites</strong> chaque semaine pour recevoir plus de courses. Les chauffeurs avec un calendrier a jour recoivent 35% de demandes en plus !
                    </p>
                </div>
            </div>
        </div>

        {{-- Period Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Today --}}
            <div class="stat-card stat-card-orange group">
                <div class="flex items-center gap-3 mb-4">
                    <div class="icon-container icon-container-md bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B]">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#64748B] uppercase tracking-wider">Aujourd'hui</h3>
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20">
                        <span class="text-[#64748B] text-sm">CA Transferts</span>
                        <span class="text-sm font-bold text-[#6366F1]">{{ number_format($todayTransfers ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20">
                        <span class="text-[#64748B] text-sm">CA Livraisons</span>
                        <span class="text-sm font-bold text-[#F59E0B]">{{ number_format($todayDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50 dark:bg-green-900/20">
                        <span class="text-[#64748B] text-sm">Total CA</span>
                        <span class="text-lg font-bold text-[#10B981]">+{{ number_format($todayIncome ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-red-50 dark:bg-red-900/20">
                        <span class="text-[#64748B] text-sm">Depenses</span>
                        <span class="text-lg font-bold text-[#EF4444]">-{{ number_format($todayExpenses ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                        <div class="flex justify-between items-center bg-gradient-to-r {{ ($todayNet ?? 0) >= 0 ? 'from-[#10B981] to-emerald-600' : 'from-[#EF4444] to-rose-600' }} p-3 rounded-xl shadow-lg">
                            <span class="font-semibold text-white">Solde</span>
                            <span class="text-xl font-black text-white">
                                {{ ($todayNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($todayNet ?? 0, 0, ',', ' ') }} DA
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Week --}}
            <div class="stat-card stat-card-indigo group">
                <div class="flex items-center gap-3 mb-4">
                    <div class="icon-container icon-container-md bg-gradient-to-br from-[#6366F1] to-[#8B5CF6]">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#64748B] uppercase tracking-wider">Cette semaine</h3>
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20">
                        <span class="text-[#64748B] text-sm">CA Transferts</span>
                        <span class="text-sm font-bold text-[#6366F1]">{{ number_format($weekTransfers ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20">
                        <span class="text-[#64748B] text-sm">CA Livraisons</span>
                        <span class="text-sm font-bold text-[#F59E0B]">{{ number_format($weekDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50 dark:bg-green-900/20">
                        <span class="text-[#64748B] text-sm">Total CA</span>
                        <span class="text-lg font-bold text-[#10B981]">+{{ number_format($weekIncome ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-red-50 dark:bg-red-900/20">
                        <span class="text-[#64748B] text-sm">Depenses</span>
                        <span class="text-lg font-bold text-[#EF4444]">-{{ number_format($weekExpenses ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                        <div class="flex justify-between items-center bg-gradient-to-r {{ ($weekNet ?? 0) >= 0 ? 'from-[#10B981] to-emerald-600' : 'from-[#EF4444] to-rose-600' }} p-3 rounded-xl shadow-lg">
                            <span class="font-semibold text-white">Solde</span>
                            <span class="text-xl font-black text-white">
                                {{ ($weekNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($weekNet ?? 0, 0, ',', ' ') }} DA
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Month --}}
            <div class="stat-card stat-card-green group">
                <div class="flex items-center gap-3 mb-4">
                    <div class="icon-container icon-container-md bg-gradient-to-br from-[#10B981] to-emerald-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#64748B] uppercase tracking-wider">Ce mois</h3>
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/20">
                        <span class="text-[#64748B] text-sm">CA Transferts</span>
                        <span class="text-sm font-bold text-[#6366F1]">{{ number_format($monthTransfers ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20">
                        <span class="text-[#64748B] text-sm">CA Livraisons</span>
                        <span class="text-sm font-bold text-[#F59E0B]">{{ number_format($monthDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50 dark:bg-green-900/20">
                        <span class="text-[#64748B] text-sm">Total CA</span>
                        <span class="text-lg font-bold text-[#10B981]">+{{ number_format($monthIncome ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-red-50 dark:bg-red-900/20">
                        <span class="text-[#64748B] text-sm">Depenses</span>
                        <span class="text-lg font-bold text-[#EF4444]">-{{ number_format($monthExpenses ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t-2 border-dashed border-gray-200 dark:border-gray-700 pt-3 mt-3">
                        <div class="flex justify-between items-center bg-gradient-to-r {{ ($monthNet ?? 0) >= 0 ? 'from-[#10B981] to-emerald-600' : 'from-[#EF4444] to-rose-600' }} p-3 rounded-xl shadow-lg">
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
            @if(($totalCommissionDue ?? 0) > 0)
            <div class="stat-card stat-card-red border-l-4 border-[#EF4444] bg-gradient-to-r from-red-50 to-white dark:from-red-900/10 dark:to-gray-800">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-[#EF4444] to-rose-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/30">
                        <x-heroicon-o-receipt-percent class="w-7 h-7 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-[#1E293B] dark:text-red-200 font-semibold">Commission due (10%)</p>
                        <p class="text-3xl font-black text-[#EF4444]">{{ number_format($totalCommissionDue ?? 0, 0, ',', ' ') }} DA</p>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs">
                            <span class="badge-modern badge-info">Transferts: {{ number_format($commissionTransfers ?? 0, 0, ',', ' ') }} DA</span>
                            <span class="badge-modern badge-warning">Livraisons: {{ number_format($commissionDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($pendingIncome ?? 0) > 0)
            <div class="stat-card stat-card-amber border-l-4 border-[#F59E0B] bg-gradient-to-r from-amber-50 to-white dark:from-amber-900/10 dark:to-gray-800">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-[#F59E0B] to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 animate-pulse">
                        <x-heroicon-o-clock class="w-7 h-7 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-[#1E293B] dark:text-amber-200 font-semibold">Courses en attente</p>
                        <p class="text-3xl font-black text-[#F59E0B]">{{ number_format($pendingIncome ?? 0, 0, ',', ' ') }} DA</p>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs">
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
            <div class="stat-card stat-card-indigo group overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#6366F1]/10 to-transparent rounded-bl-full"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#6366F1] to-[#8B5CF6] rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-map-pin class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <p class="text-sm text-[#64748B] font-semibold">Transferts ce mois</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-[#6366F1] to-[#8B5CF6] bg-clip-text text-transparent">{{ $monthTransfersCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-orange group overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#FF6B2C]/10 to-transparent rounded-bl-full"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B] rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
                        <x-heroicon-o-inbox-stack class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <p class="text-sm text-[#64748B] font-semibold">Livraisons ce mois</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-[#FF6B2C] to-[#F59E0B] bg-clip-text text-transparent">{{ $monthDeliveriesCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Courses --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Recent Transfers --}}
            <div class="card-modern overflow-hidden p-0">
                <div class="px-6 py-4 bg-gradient-to-r from-[#6366F1] to-[#8B5CF6] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5" />
                        Derniers transferts
                    </h3>
                    <a href="{{ route('filament.chauffeur.resources.transfer-bookings.index') }}" class="text-sm text-white/80 hover:text-white font-medium flex items-center gap-1 transition-colors">
                        Voir tout
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentTransfers ?? [] as $transfer)
                        <div class="data-row px-6 py-4">
                            <div class="flex items-center justify-between w-full">
                                <div>
                                    <p class="font-semibold text-[#1E293B] dark:text-white">{{ $transfer->reference }}</p>
                                    <p class="text-sm text-[#64748B] flex items-center gap-1">
                                        <x-heroicon-o-map-pin class="w-3 h-3" />
                                        {{ $transfer->departure }} &rarr; {{ $transfer->destination }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $transfer->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-[#10B981]">{{ number_format($transfer->price ?? 0, 0, ',', ' ') }} DA</span>
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
                            <div class="empty-state-icon" style="background: rgba(99, 102, 241, 0.1);">
                                <x-heroicon-o-arrow-path-rounded-square class="w-6 h-6 text-[#6366F1]" />
                            </div>
                            <p class="text-[#64748B]">Aucun transfert recent</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Deliveries --}}
            <div class="card-modern overflow-hidden p-0">
                <div class="px-6 py-4 bg-gradient-to-r from-[#FF6B2C] to-[#F59E0B] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-heroicon-o-truck class="w-5 h-5" />
                        Dernieres livraisons
                    </h3>
                    <a href="{{ route('filament.chauffeur.resources.delivery-bookings.index') }}" class="text-sm text-white/80 hover:text-white font-medium flex items-center gap-1 transition-colors">
                        Voir tout
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDeliveries ?? [] as $delivery)
                        <div class="data-row px-6 py-4">
                            <div class="flex items-center justify-between w-full">
                                <div>
                                    <p class="font-semibold text-[#1E293B] dark:text-white">{{ $delivery->reference }}</p>
                                    <p class="text-sm text-[#64748B] flex items-center gap-1">
                                        <x-heroicon-o-cube class="w-3 h-3" />
                                        {{ $delivery->pickup_city }} &rarr; {{ $delivery->delivery_city }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-[#10B981]">{{ number_format($delivery->price ?? 0, 0, ',', ' ') }} DA</span>
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
                            <div class="empty-state-icon" style="background: rgba(255, 107, 44, 0.1);">
                                <x-heroicon-o-truck class="w-6 h-6 text-[#FF6B2C]" />
                            </div>
                            <p class="text-[#64748B]">Aucune livraison recente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="card-modern overflow-hidden p-0">
            <div class="px-6 py-4 bg-gradient-to-r from-[#10B981] to-emerald-600 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-5 h-5" />
                    Dernieres transactions
                </h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentTransactions ?? [] as $transaction)
                    <div class="data-row px-6 py-4">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg {{ $transaction->type === 'income' ? 'bg-gradient-to-br from-[#10B981] to-emerald-600' : 'bg-gradient-to-br from-[#EF4444] to-rose-600' }}">
                                    @if($transaction->type === 'income')
                                        <x-heroicon-o-arrow-down class="w-6 h-6 text-white" />
                                    @else
                                        <x-heroicon-o-arrow-up class="w-6 h-6 text-white" />
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-[#1E293B] dark:text-white">{{ $transaction->description }}</p>
                                    <p class="text-sm text-[#64748B]">
                                        {{ $transaction->transaction_date->format('d/m/Y') }}
                                        @if($transaction->expenseCategory)
                                            <span class="mx-1">&bull;</span>
                                            <span class="text-gray-400">{{ $transaction->expenseCategory->name }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="text-xl font-bold {{ $transaction->type === 'income' ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                                {{ $transaction->getFormattedAmount() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state py-12">
                        <div class="empty-state-icon" style="background: rgba(16, 185, 129, 0.1);">
                            <x-heroicon-o-banknotes class="w-6 h-6 text-[#10B981]" />
                        </div>
                        <p class="text-[#64748B]">Aucune transaction pour le moment</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
