<x-filament-panels::page>
    <div class="mb-6">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <x-heroicon-o-star class="w-6 h-6 text-amber-600" />
                </div>
                <div>
                    <h3 class="font-semibold text-amber-900">Section "Notre sélection pour vous"</h3>
                    <p class="text-sm text-amber-700">
                        {{ $this->getSelectedVehiclesCount() }} véhicule(s) dans la sélection.
                        Ces véhicules apparaissent en premier sur la page d'accueil avec une bordure dorée.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
