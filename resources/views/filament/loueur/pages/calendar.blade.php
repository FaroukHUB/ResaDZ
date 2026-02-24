<x-filament-panels::page>
    <style>
        .cal-grid { display: grid; gap: 0; }
        .cal-cell {
            width: 44px; height: 44px;
            border-radius: 8px;
            margin: 1px;
            transition: all 0.15s ease;
            position: relative;
            cursor: default;
        }
        .cal-cell-free {
            background: #d1fae5;
            cursor: pointer;
        }
        .cal-cell-free:hover {
            background: #6ee7b7;
            transform: scale(1.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            z-index: 2;
        }
        .cal-cell-past { background: #f3f4f6; }
        .cal-cell-pending { background: #fbbf24; }
        .cal-cell-confirmed { background: #3b82f6; }
        .cal-cell-active { background: #8b5cf6; }
        .cal-cell-blocked {
            background: repeating-linear-gradient(
                45deg, #374151, #374151 4px, #4b5563 4px, #4b5563 8px
            );
            cursor: pointer;
        }
        .cal-cell-blocked:hover {
            opacity: 0.7;
            transform: scale(1.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 2;
        }
        .cal-cell-maintenance {
            background: repeating-linear-gradient(
                45deg, #f59e0b, #f59e0b 4px, #fbbf24 4px, #fbbf24 8px
            );
        }
        .cal-cell-booking:hover {
            transform: scale(1.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 2;
        }
        .cal-vehicle-card {
            min-width: 220px;
            max-width: 220px;
        }
        .cal-day-header {
            width: 46px; min-width: 46px;
            text-align: center;
            padding: 6px 0;
        }
        .cal-row {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .cal-row:last-child { border-bottom: none; }
        .dark .cal-cell-free { background: #065f46; }
        .dark .cal-cell-free:hover { background: #059669; }
        .dark .cal-cell-past { background: #1f2937; }
        .dark .cal-cell-blocked {
            background: repeating-linear-gradient(45deg, #9ca3af, #9ca3af 4px, #d1d5db 4px, #d1d5db 8px);
        }
        .dark .cal-row { border-bottom-color: #374151; }

        @media (max-width: 640px) {
            .cal-cell { width: 36px; height: 36px; border-radius: 6px; }
            .cal-day-header { width: 38px; min-width: 38px; }
            .cal-vehicle-card { min-width: 160px; max-width: 160px; }
        }
    </style>

    <div class="space-y-5" x-data="{
        showImportModal: false,
        importVehicleId: null,
        importUrl: '',
        importVehicleName: '',
        openImport(id, name) {
            this.importVehicleId = id;
            this.importVehicleName = name;
            this.importUrl = '';
            this.showImportModal = true;
        }
    }">

        {{-- Header bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <button wire:click="previousMonth" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="px-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white capitalize">{{ $monthName }}</h2>
                </div>
                <button wire:click="nextMonth" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button wire:click="goToToday" class="ml-3 px-4 py-2 text-sm font-semibold rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition shadow-sm">
                    Aujourd'hui
                </button>
            </div>
            <div class="flex items-center gap-3">
                <div x-data="{ copied: false }">
                    <button
                        @click="navigator.clipboard.writeText('{{ $icalUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span x-text="copied ? 'Lien copié !' : 'Copier lien iCal'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 text-sm px-1">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md bg-[#d1fae5]"></div>
                <span class="text-gray-600 dark:text-gray-400 font-medium">Disponible</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md bg-[#fbbf24]"></div>
                <span class="text-gray-600 dark:text-gray-400 font-medium">En attente</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md bg-[#3b82f6]"></div>
                <span class="text-gray-600 dark:text-gray-400 font-medium">Confirmée</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md bg-[#8b5cf6]"></div>
                <span class="text-gray-600 dark:text-gray-400 font-medium">En cours</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md" style="background: repeating-linear-gradient(45deg, #374151, #374151 3px, #4b5563 3px, #4b5563 6px)"></div>
                <span class="text-gray-600 dark:text-gray-400 font-medium">Bloqué</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md" style="background: repeating-linear-gradient(45deg, #f59e0b, #f59e0b 3px, #fbbf24 3px, #fbbf24 6px)"></div>
                <span class="text-gray-600 dark:text-gray-400 font-medium">Maintenance</span>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <div style="min-width: {{ 220 + (count($days) * 48) }}px;">

                    {{-- Days header --}}
                    <div class="flex items-end border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-20">
                        <div class="cal-vehicle-card px-4 py-3">
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Véhicule</span>
                        </div>
                        @foreach($days as $day)
                            @php
                                $isToday = $day->format('Y-m-d') === $today;
                                $isWeekend = $day->isWeekend();
                            @endphp
                            <div class="cal-day-header {{ $isToday ? 'relative' : '' }}">
                                @if($isToday)
                                    <div class="absolute inset-x-1 -bottom-px h-[3px] bg-primary-500 rounded-t-full"></div>
                                @endif
                                <div class="text-[10px] font-semibold uppercase {{ $isWeekend ? 'text-red-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ mb_substr($day->translatedFormat('D'), 0, 2) }}
                                </div>
                                <div class="text-sm font-bold {{ $isToday ? 'text-white bg-primary-600 rounded-full w-7 h-7 flex items-center justify-center mx-auto' : ($isWeekend ? 'text-red-500 dark:text-red-400' : 'text-gray-700 dark:text-gray-300') }}">
                                    {{ $day->format('j') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Vehicle rows --}}
                    @forelse($vehicles as $vehicle)
                        <div class="cal-row" wire:key="row-{{ $vehicle->id }}">
                            {{-- Vehicle card --}}
                            <div class="cal-vehicle-card px-4 flex items-center gap-3">
                                @if($vehicle->image)
                                    <img src="{{ asset('storage/' . $vehicle->image) }}" alt="" class="w-11 h-11 rounded-xl object-cover flex-shrink-0 shadow-sm">
                                @else
                                    <div class="w-11 h-11 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H6.375c-.621 0-1.125-.504-1.125-1.125V11.25"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $vehicle->full_name }}">{{ $vehicle->full_name }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA/j</span>
                                        <button
                                            @click="openImport({{ $vehicle->id }}, '{{ addslashes($vehicle->full_name) }}')"
                                            class="text-[11px] text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-medium flex items-center gap-0.5"
                                            title="Importer un calendrier externe"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Importer
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Day cells --}}
                            <div class="flex items-center">
                                @foreach($days as $day)
                                    @php
                                        $dateStr = $day->format('Y-m-d');
                                        $key = $vehicle->id . '_' . $dateStr;
                                        $booking = $bookingMap[$key] ?? null;
                                        $block = $blockMap[$key] ?? null;
                                        $isToday = $dateStr === $today;
                                        $isPast = $day->isPast() && !$isToday;
                                    @endphp

                                    @if($booking)
                                        {{-- Booked cell --}}
                                        <a
                                            href="{{ route('filament.loueur.resources.bookings.edit', $booking->id) }}"
                                            class="cal-cell cal-cell-booking cal-cell-{{ $booking->status }} flex items-center justify-center {{ $isToday ? 'ring-2 ring-primary-400 ring-offset-1' : '' }}"
                                            title="{{ $booking->client_name }} — {{ $booking->start_date->format('d/m') }} → {{ $booking->end_date->format('d/m') }}"
                                            wire:key="c-{{ $key }}"
                                        >
                                            <svg class="w-4 h-4 text-white opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </a>
                                    @elseif($block && $block->type === 'blocked')
                                        {{-- Blocked cell --}}
                                        <button
                                            wire:click="toggleBlock({{ $vehicle->id }}, '{{ $dateStr }}')"
                                            wire:loading.attr="disabled"
                                            class="cal-cell cal-cell-blocked flex items-center justify-center {{ $isToday ? 'ring-2 ring-primary-400 ring-offset-1' : '' }}"
                                            title="Bloqué — Cliquez pour débloquer"
                                            wire:key="c-{{ $key }}"
                                        >
                                            <svg class="w-4 h-4 text-white opacity-70" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @elseif($block && $block->type === 'maintenance')
                                        {{-- Maintenance cell --}}
                                        <div
                                            class="cal-cell cal-cell-maintenance flex items-center justify-center {{ $isToday ? 'ring-2 ring-primary-400 ring-offset-1' : '' }}"
                                            title="Maintenance{{ $block->reason ? ' : ' . $block->reason : '' }}"
                                            wire:key="c-{{ $key }}"
                                        >
                                            <svg class="w-4 h-4 text-white opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.66-5.66a2 2 0 112.83-2.83l5.66 5.66m-1.42 1.42l5.66 5.66a2 2 0 11-2.83 2.83l-5.66-5.66"/></svg>
                                        </div>
                                    @elseif($isPast)
                                        {{-- Past cell --}}
                                        <div class="cal-cell cal-cell-past" wire:key="c-{{ $key }}"></div>
                                    @else
                                        {{-- Free cell --}}
                                        <button
                                            wire:click="toggleBlock({{ $vehicle->id }}, '{{ $dateStr }}')"
                                            wire:loading.attr="disabled"
                                            class="cal-cell cal-cell-free {{ $isToday ? 'ring-2 ring-primary-400 ring-offset-1' : '' }}"
                                            title="{{ $day->translatedFormat('l j F') }} — Cliquez pour bloquer"
                                            wire:key="c-{{ $key }}"
                                        ></button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H6.375c-.621 0-1.125-.504-1.125-1.125V11.25"/></svg>
                            </div>
                            <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">Aucun véhicule actif</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Ajoutez des véhicules pour utiliser le calendrier</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Info bar --}}
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Tip --}}
            <div class="flex-1 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-5">
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm">Comment ça marche ?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Cliquez sur une case <span class="inline-block w-3 h-3 rounded bg-[#d1fae5] align-middle"></span> verte pour la bloquer.
                            Cliquez sur une case <span class="inline-block w-3 h-3 rounded align-middle" style="background: repeating-linear-gradient(45deg, #374151, #374151 2px, #4b5563 2px, #4b5563 4px)"></span> hachurée pour la débloquer.
                            Les cases colorées sont des réservations — cliquez dessus pour voir les détails.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Sync card --}}
            <div class="lg:w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm">Synchronisation</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Google Agenda, Apple, Outlook</p>
                    </div>
                </div>
                <div x-data="{ showUrl: false }">
                    <button @click="showUrl = !showUrl" class="w-full text-left text-xs text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-medium">
                        <span x-text="showUrl ? 'Masquer le lien' : 'Afficher le lien d\'abonnement'"></span> &rarr;
                    </button>
                    <div x-show="showUrl" x-transition class="mt-2 space-y-2">
                        <div class="flex gap-1.5">
                            <input type="text" value="{{ $icalUrl }}" readonly class="flex-1 text-[11px] bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg px-2.5 py-2 text-gray-500 dark:text-gray-400 font-mono" />
                            <button @click="navigator.clipboard.writeText('{{ $icalUrl }}')" class="px-3 py-2 text-xs bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium">Copier</button>
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 leading-relaxed">
                            Collez ce lien dans <strong>Google Agenda</strong> (+ > URL), <strong>Apple Calendar</strong> (Fichier > Abonnement) ou <strong>Outlook</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Import Modal --}}
        <template x-teleport="body">
            <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showImportModal = false"></div>
                <div x-show="showImportModal" x-transition
                     class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Importer un calendrier</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Véhicule : <strong x-text="importVehicleName" class="text-gray-900 dark:text-white"></strong>
                            </p>
                        </div>
                        <button @click="showImportModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Lien iCal</label>
                        <input
                            x-model="importUrl"
                            type="url"
                            placeholder="https://calendar.google.com/calendar/ical/..."
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 px-4 py-3"
                        />
                        <div class="mt-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                            <p class="font-semibold text-gray-700 dark:text-gray-300">Comment obtenir le lien :</p>
                            <p>1. Ouvrez <strong>Google Agenda</strong></p>
                            <p>2. Paramètres du calendrier > <strong>Adresse publique au format iCal</strong></p>
                            <p>3. Copiez le lien et collez-le ci-dessus</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button @click="showImportModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Annuler
                        </button>
                        <button
                            @click="if(importUrl && importVehicleId) { $wire.importGoogleCalendar(importVehicleId, importUrl); showImportModal = false; }"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition shadow-sm"
                        >
                            Importer
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Loading --}}
    <div wire:loading.flex class="fixed inset-0 z-[110] items-center justify-center bg-black/20 backdrop-blur-[2px]">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl px-8 py-5 flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mise à jour du calendrier...</span>
        </div>
    </div>
</x-filament-panels::page>
