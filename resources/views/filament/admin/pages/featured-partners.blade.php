<x-filament-panels::page>
    <div class="mb-6">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <x-heroicon-o-building-storefront class="w-6 h-6 text-green-600" />
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">Section "Nos loueurs partenaires"</h3>
                    <p class="text-sm text-green-700">
                        {{ $this->getFeaturedPartnersCount() }} loueur(s) mis en avant.
                        Les 5 premiers apparaissent sur la page d'accueil dans un slider. Les autres sont visibles via "Voir tous nos partenaires".
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
