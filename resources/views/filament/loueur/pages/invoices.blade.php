<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="welcome-banner" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <x-heroicon-o-document-text class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Mes factures</h2>
                        <p class="text-white/80 text-sm">Consultez et telechargez vos factures ResaDZ</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guide Section --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-white" />
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-blue-900 dark:text-blue-100">Comprendre vos factures</p>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 mt-2 space-y-1">
                        <li>• <strong>Commission</strong> : 10% preleves sur chaque reservation completee</li>
                        <li>• <strong>Boosts</strong> : Factures separees pour les mises en avant de vehicules</li>
                        <li>• <strong>En attente</strong> : Factures a regler avant la date d'echeance</li>
                        <li>• <strong>PDF</strong> : Cliquez sur le bouton pour telecharger une version imprimable</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total factures</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $invoices->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">En attente</p>
                        <p class="text-2xl font-bold text-amber-600">{{ number_format($totalUnpaid, 0, ',', ' ') }} DA</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payé</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($totalPaid, 0, ',', ' ') }} DA</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoices List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white">Historique des factures</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">N° Facture</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-4 py-4 text-gray-500">
                                    {{ $invoice->issue_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-gray-700 dark:text-gray-300">
                                    @if($invoice->items->count() > 0)
                                        {{ \Str::limit($invoice->items->first()->description, 40) }}
                                        @if($invoice->items->count() > 1)
                                            <span class="text-gray-400">(+{{ $invoice->items->count() - 1 }})</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        {{ match($invoice->status) {
                                            'draft' => 'bg-gray-100 text-gray-700',
                                            'sent' => 'bg-yellow-100 text-yellow-700',
                                            'paid' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'overdue' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        } }}">
                                        {{ $statuses[$invoice->status] ?? $invoice->status }}
                                    </span>
                                    @if($invoice->due_date < now() && in_array($invoice->status, ['sent']))
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 ml-1">
                                            En retard
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($invoice->total, 2, ',', ' ') }} DA
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('admin.invoices.pdf', $invoice) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>Aucune facture pour le moment</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Instructions --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-3">Modalités de paiement</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800 dark:text-blue-300">
                <div>
                    <p class="font-medium mb-1">Virement bancaire</p>
                    <p class="text-blue-600 dark:text-blue-400">Contactez-nous pour les coordonnées bancaires</p>
                </div>
                <div>
                    <p class="font-medium mb-1">Paiement en espèces</p>
                    <p class="text-blue-600 dark:text-blue-400">Rendez-vous dans nos locaux</p>
                </div>
            </div>
            <p class="mt-4 text-sm text-blue-700 dark:text-blue-400">
                Pour toute question concernant vos factures, contactez-nous via la messagerie.
            </p>
        </div>
    </div>
</x-filament-panels::page>
