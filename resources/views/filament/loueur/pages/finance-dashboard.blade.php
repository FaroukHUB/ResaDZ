<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Period Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Today --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">Aujourd'hui</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Entrées</span>
                        <span class="text-lg font-bold text-green-600">+{{ number_format($todayIncome, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Sorties</span>
                        <span class="text-lg font-bold text-red-600">-{{ number_format($todayExpenses, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Solde</span>
                            <span class="text-xl font-black {{ $todayNet >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $todayNet >= 0 ? '+' : '' }}{{ number_format($todayNet, 0, ',', ' ') }} DA
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
                        <span class="text-gray-600 dark:text-gray-300">Entrées</span>
                        <span class="text-lg font-bold text-green-600">+{{ number_format($weekIncome, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Sorties</span>
                        <span class="text-lg font-bold text-red-600">-{{ number_format($weekExpenses, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Solde</span>
                            <span class="text-xl font-black {{ $weekNet >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $weekNet >= 0 ? '+' : '' }}{{ number_format($weekNet, 0, ',', ' ') }} DA
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
                        <span class="text-gray-600 dark:text-gray-300">Entrées</span>
                        <span class="text-lg font-bold text-green-600">+{{ number_format($monthIncome, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-300">Sorties</span>
                        <span class="text-lg font-bold text-red-600">-{{ number_format($monthExpenses, 0, ',', ' ') }} DA</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Solde</span>
                            <span class="text-xl font-black {{ $monthNet >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $monthNet >= 0 ? '+' : '' }}{{ number_format($monthNet, 0, ',', ' ') }} DA
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Income --}}
        @if($pendingIncome > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-amber-800 dark:text-amber-200">Revenus en attente (réservations confirmées/en cours)</p>
                    <p class="text-lg font-bold text-amber-900 dark:text-amber-100">{{ number_format($pendingIncome, 0, ',', ' ') }} DA</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Recent Transactions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dernières transactions</h3>
                <a href="{{ route('filament.loueur.resources.transactions.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Voir tout
                </a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentTransactions as $transaction)
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
