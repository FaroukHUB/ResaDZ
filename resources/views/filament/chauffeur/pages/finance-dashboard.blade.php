<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Period Stats with CA breakdown --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Today --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Aujourd'hui</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">CA Transferts</span>
                        <span class="text-sm font-semibold text-blue-600">{{ number_format($todayTransfers ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">CA Livraisons</span>
                        <span class="text-sm font-semibold text-amber-600">{{ number_format($todayDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Total CA</span>
                        <span class="text-lg font-bold text-green-600">+{{ number_format($todayIncome ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Depenses</span>
                        <span class="text-lg font-bold text-red-600">-{{ number_format($todayExpenses ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Solde</span>
                            <span class="text-xl font-black {{ ($todayNet ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($todayNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($todayNet ?? 0, 0, ',', ' ') }} DA
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Week --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Cette semaine</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">CA Transferts</span>
                        <span class="text-sm font-semibold text-blue-600">{{ number_format($weekTransfers ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">CA Livraisons</span>
                        <span class="text-sm font-semibold text-amber-600">{{ number_format($weekDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Total CA</span>
                        <span class="text-lg font-bold text-green-600">+{{ number_format($weekIncome ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Depenses</span>
                        <span class="text-lg font-bold text-red-600">-{{ number_format($weekExpenses ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Solde</span>
                            <span class="text-xl font-black {{ ($weekNet ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($weekNet ?? 0) >= 0 ? '+' : '' }}{{ number_format($weekNet ?? 0, 0, ',', ' ') }} DA
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Month --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Ce mois</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">CA Transferts</span>
                        <span class="text-sm font-semibold text-blue-600">{{ number_format($monthTransfers ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">CA Livraisons</span>
                        <span class="text-sm font-semibold text-amber-600">{{ number_format($monthDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Total CA</span>
                        <span class="text-lg font-bold text-green-600">+{{ number_format($monthIncome ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Depenses</span>
                        <span class="text-lg font-bold text-red-600">-{{ number_format($monthExpenses ?? 0, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Solde</span>
                            <span class="text-xl font-black {{ ($monthNet ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
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
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                        <x-heroicon-o-receipt-percent class="w-5 h-5 text-red-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-red-800 dark:text-red-200 font-semibold">Commission due (10%)</p>
                        <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ number_format($totalCommissionDue ?? 0, 0, ',', ' ') }} DA</p>
                        <div class="mt-1 text-xs text-red-600 dark:text-red-400 space-x-3">
                            <span>Transferts: {{ number_format($commissionTransfers ?? 0, 0, ',', ' ') }} DA</span>
                            <span>|</span>
                            <span>Livraisons: {{ number_format($commissionDeliveries ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Pending Income --}}
            @if(($pendingIncome ?? 0) > 0)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                        <x-heroicon-o-clock class="w-5 h-5 text-amber-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-amber-800 dark:text-amber-200 font-semibold">Courses en attente</p>
                        <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ number_format($pendingIncome ?? 0, 0, ',', ' ') }} DA</p>
                        <div class="mt-1 text-xs text-amber-600 dark:text-amber-400 space-x-3">
                            <span>Transferts: {{ number_format($pendingTransfersIncome ?? 0, 0, ',', ' ') }} DA</span>
                            <span>|</span>
                            <span>Livraisons: {{ number_format($pendingDeliveriesIncome ?? 0, 0, ',', ' ') }} DA</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Courses Counter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-sm text-blue-800 dark:text-blue-200 font-semibold">Transferts ce mois</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $monthTransfersCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                            <x-heroicon-o-inbox-stack class="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <p class="text-sm text-amber-800 dark:text-amber-200 font-semibold">Livraisons ce mois</p>
                            <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ $monthDeliveriesCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Courses (Transfers + Deliveries) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Recent Transfers --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Derniers transferts</h3>
                    <a href="{{ route('filament.chauffeur.resources.transfer-bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Voir tout
                    </a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentTransfers ?? [] as $transfer)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $transfer->reference }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $transfer->departure }} &rarr; {{ $transfer->destination }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $transfer->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-green-600">{{ number_format($transfer->price ?? 0, 0, ',', ' ') }} DA</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $transfer->status === 'completed' ? 'bg-green-100 text-green-800' :
                                           ($transfer->status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                           ($transfer->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $transfer->status === 'completed' ? 'Termine' :
                                           ($transfer->status === 'confirmed' ? 'Confirme' :
                                           ($transfer->status === 'pending' ? 'En attente' : $transfer->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Aucun transfert recent
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Deliveries --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dernieres livraisons</h3>
                    <a href="{{ route('filament.chauffeur.resources.delivery-bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Voir tout
                    </a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentDeliveries ?? [] as $delivery)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $delivery->reference }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $delivery->pickup_city }} &rarr; {{ $delivery->delivery_city }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-green-600">{{ number_format($delivery->price ?? 0, 0, ',', ' ') }} DA</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' :
                                           ($delivery->status === 'in_transit' ? 'bg-purple-100 text-purple-800' :
                                           ($delivery->status === 'picked_up' ? 'bg-blue-100 text-blue-800' :
                                           ($delivery->status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
                                           ($delivery->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')))) }}">
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
                        <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Aucune livraison recente
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dernieres transactions</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentTransactions ?? [] as $transaction)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $transaction->type === 'income' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                @if($transaction->type === 'income')
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8l-8 8-8-8"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4m-8 8l8-8 8 8"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transaction->transaction_date->format('d/m/Y') }}
                                    @if($transaction->expenseCategory)
                                        <span class="mx-1">&bull;</span>
                                        <span class="text-gray-400">{{ $transaction->expenseCategory->name }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="text-lg font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->getFormattedAmount() }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        Aucune transaction pour le moment
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
