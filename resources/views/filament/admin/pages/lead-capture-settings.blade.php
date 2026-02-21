<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">
                Enregistrer
            </x-filament::button>
        </div>
    </form>

    {{-- Preview --}}
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aperçu</h3>
        <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-8 flex items-center justify-center">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                {{-- Header --}}
                <div
                    class="p-6 text-white text-center"
                    style="background: linear-gradient(to right, {{ $this->data['header_gradient_from'] ?? '#dc2626' }}, {{ $this->data['header_gradient_to'] ?? '#ef4444' }});"
                >
                    <div class="text-4xl mb-2">{{ $this->data['emoji'] ?? '🎉' }}</div>
                    <h3 class="text-xl font-bold">{{ $this->data['title'] ?? 'Ne ratez plus aucune offre !' }}</h3>
                    <p class="text-white/80 text-sm mt-1">{{ $this->data['subtitle'] ?? 'Rejoignez notre communauté pour des promos exclusives' }}</p>
                </div>

                {{-- Content --}}
                <div class="p-6">
                    <div class="flex gap-2">
                        <input
                            type="email"
                            placeholder="{{ $this->data['placeholder'] ?? 'Votre email' }}"
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-lg"
                            disabled
                        >
                        <button
                            type="button"
                            class="px-6 py-3 text-white font-semibold rounded-lg"
                            style="background-color: {{ $this->data['button_color'] ?? '#dc2626' }};"
                        >
                            {{ $this->data['button_text'] ?? 'OK' }}
                        </button>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-6 py-3 text-center">
                    <span class="text-gray-400 text-sm">{{ $this->data['dismiss_text'] ?? 'Ne plus afficher' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
