<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Invoice Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Facture {{ $invoice->invoice_number }}</h2>
                    <p class="text-gray-500 mt-1">Émise le {{ $invoice->issue_date->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                        {{ match($invoice->status) {
                            'draft' => 'bg-gray-100 text-gray-700',
                            'sent' => 'bg-yellow-100 text-yellow-700',
                            'paid' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            'overdue' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700'
                        } }}">
                        {{ \App\Models\Invoice::getStatuses()[$invoice->status] ?? $invoice->status }}
                    </span>
                    @if($invoice->paid_date)
                        <p class="text-sm text-green-600 mt-2">Payée le {{ $invoice->paid_date->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- From (ResaDZ) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">De</h3>
                <p class="font-bold text-gray-900 dark:text-white">ResaDZ</p>
                <p class="text-gray-600 dark:text-gray-400">Marketplace de location de voitures</p>
                <p class="text-gray-600 dark:text-gray-400">Algérie</p>
                <p class="text-gray-600 dark:text-gray-400">contact@resadz.com</p>
            </div>

            {{-- To (Loueur) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Facturé à</h3>
                <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->billing_name }}</p>
                @if($invoice->billing_address)
                    <p class="text-gray-600 dark:text-gray-400">{{ $invoice->billing_address }}</p>
                @endif
                @if($invoice->billing_city)
                    <p class="text-gray-600 dark:text-gray-400">{{ $invoice->billing_city }}</p>
                @endif
                @if($invoice->billing_phone)
                    <p class="text-gray-600 dark:text-gray-400">{{ $invoice->billing_phone }}</p>
                @endif
                @if($invoice->billing_email)
                    <p class="text-gray-600 dark:text-gray-400">{{ $invoice->billing_email }}</p>
                @endif
                @if($invoice->billing_nif)
                    <p class="text-gray-600 dark:text-gray-400">NIF: {{ $invoice->billing_nif }}</p>
                @endif
            </div>
        </div>

        {{-- Invoice Items --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qté</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prix unitaire</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remise</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $item->description }}</td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 2, ',', ' ') }} DA</td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->discount, 2, ',', ' ') }} DA</td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">{{ number_format($item->total, 2, ',', ' ') }} DA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune ligne de facture</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Sous-total</td>
                        <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($invoice->subtotal, 2, ',', ' ') }} DA</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Remise globale</td>
                            <td class="px-6 py-3 text-right font-medium text-red-600">-{{ number_format($invoice->discount_amount, 2, ',', ' ') }} DA</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-700 dark:text-gray-300">TVA ({{ $invoice->tax_rate }}%)</td>
                        <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} DA</td>
                    </tr>
                    <tr class="bg-gray-100 dark:bg-gray-600">
                        <td colspan="4" class="px-6 py-4 text-right text-lg font-bold text-gray-900 dark:text-white">Total TTC</td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-gray-900 dark:text-white">{{ number_format($invoice->total, 2, ',', ' ') }} DA</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Payment Info & Notes --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Informations de paiement</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Échéance</dt>
                        <dd class="font-medium text-gray-900 dark:text-white {{ $invoice->isOverdue() ? 'text-red-600' : '' }}">
                            {{ $invoice->due_date->format('d/m/Y') }}
                            @if($invoice->isOverdue())
                                <span class="text-red-600">(En retard)</span>
                            @endif
                        </dd>
                    </div>
                    @if($invoice->payment_method)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Méthode de paiement</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">
                                {{ \App\Models\Invoice::getPaymentMethods()[$invoice->payment_method] ?? $invoice->payment_method }}
                            </dd>
                        </div>
                    @endif
                    @if($invoice->payment_reference)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Référence paiement</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $invoice->payment_reference }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($invoice->notes)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Notes</h3>
                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $invoice->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
