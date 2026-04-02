<x-filament-panels::page>

    {{-- Actions en haut --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('filament.admin.resources.prospects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajouter un prospect
            </a>
            <a href="{{ route('filament.admin.resources.prospects.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 font-semibold rounded-lg border border-gray-200 hover:bg-gray-50 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Vue liste + Import CSV
            </a>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500">Total prospects</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['today'] }}</div>
            <div class="text-xs text-gray-500">Contactés aujourd'hui</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold {{ $stats['relances'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['relances'] }}</div>
            <div class="text-xs text-gray-500">Relances du jour</div>
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="flex gap-4 overflow-x-auto pb-4" style="min-height: 500px;">
        @foreach($columns as $status => $column)
            <div class="flex-shrink-0 w-72 flex flex-col"
                 x-data
                 x-on:dragover.prevent="$el.classList.add('ring-2', 'ring-blue-400')"
                 x-on:dragleave="$el.classList.remove('ring-2', 'ring-blue-400')"
                 x-on:drop.prevent="
                    $el.classList.remove('ring-2', 'ring-blue-400');
                    const id = event.dataTransfer.getData('prospect-id');
                    if (id) { $wire.moveProspect(parseInt(id), '{{ $status }}'); }
                 ">

                {{-- Header colonne --}}
                <div class="rounded-t-xl px-4 py-3 flex items-center justify-between" style="background: {{ $column['color'] }};">
                    <span class="text-white font-bold text-sm">{{ $column['label'] }}</span>
                    <span class="bg-white/30 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ ($prospects[$status] ?? collect())->count() }}
                    </span>
                </div>

                {{-- Cards --}}
                <div class="{{ $column['bg'] }} rounded-b-xl border border-gray-200 border-t-0 p-3 flex-1 space-y-3 overflow-y-auto" style="max-height: 600px;">
                    @forelse(($prospects[$status] ?? collect()) as $prospect)
                        <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition group"
                             draggable="true"
                             x-on:dragstart="event.dataTransfer.setData('prospect-id', '{{ $prospect->id }}')">

                            {{-- Nom + source --}}
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $prospect->nom }}</p>
                                    @if($prospect->wilaya)
                                        <p class="text-xs text-gray-400">{{ $prospect->wilaya }}</p>
                                    @endif
                                </div>
                                @php
                                    $sourceColors = ['facebook' => 'bg-blue-100 text-blue-700', 'google' => 'bg-red-100 text-red-700', 'telegram' => 'bg-sky-100 text-sky-700', 'terrain' => 'bg-amber-100 text-amber-700', 'whatsapp' => 'bg-green-100 text-green-700', 'autre' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $sourceColors[$prospect->source] ?? $sourceColors['autre'] }}">
                                    {{ \App\Models\Prospect::SOURCES[$prospect->source] ?? $prospect->source }}
                                </span>
                            </div>

                            {{-- Téléphone --}}
                            <div class="flex items-center gap-2 mb-2">
                                <a href="https://wa.me/213{{ ltrim($prospect->telephone, '0') }}" target="_blank"
                                   class="text-xs text-green-600 hover:text-green-700 font-mono flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51z"/></svg>
                                    {{ $prospect->telephone }}
                                </a>
                            </div>

                            {{-- Infos --}}
                            <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                @if($prospect->nb_vehicules)
                                    <span>{{ $prospect->nb_vehicules }} véh.</span>
                                    <span>•</span>
                                @endif
                                @if($prospect->date_dernier_contact)
                                    <span>Contact {{ $prospect->date_dernier_contact->format('d/m') }}</span>
                                @endif
                                @if($prospect->relance_le)
                                    <span class="{{ $prospect->relance_le->isPast() ? 'text-red-500 font-bold' : '' }}">
                                        Relance {{ $prospect->relance_le->format('d/m') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Notes (aperçu) --}}
                            @if($prospect->notes)
                                <p class="text-[10px] text-gray-400 mt-1 italic truncate">{{ $prospect->notes }}</p>
                            @endif

                            {{-- Actions (visible au hover) --}}
                            <div class="hidden group-hover:flex items-center gap-1 mt-2 pt-2 border-t border-gray-100">
                                <a href="{{ route('filament.admin.resources.prospects.edit', $prospect) }}" class="text-[10px] text-blue-600 hover:underline">Modifier</a>
                                <span class="text-gray-300">|</span>
                                <button wire:click="deleteProspect({{ $prospect->id }})"
                                        wire:confirm="Supprimer ce prospect ?"
                                        class="text-[10px] text-red-500 hover:underline">Supprimer</button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-xs text-gray-400">Aucun prospect</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</x-filament-panels::page>
