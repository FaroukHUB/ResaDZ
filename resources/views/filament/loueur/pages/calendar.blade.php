<x-filament-panels::page>
    <div class="space-y-4" x-data="{
        showImportModal: false,
        importVehicleId: null,
        importUrl: '',
        importVehicleName: '',
        openImport(vehicleId, vehicleName) {
            this.importVehicleId = vehicleId;
            this.importVehicleName = vehicleName;
            this.importUrl = '';
            this.showImportModal = true;
        }
    }">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <button wire:click="previousMonth" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white capitalize min-w-[160px] text-center">{{ $monthName }}</h2>
                <button wire:click="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button wire:click="goToToday" class="ml-2 px-3 py-1.5 text-xs font-medium rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/40 transition">
                    Aujourd'hui
                </button>
            </div>

            <div class="flex items-center gap-2">
                {{-- iCal export --}}
                <div x-data="{ copied: false }" class="relative">
                    <button
                        @click="navigator.clipboard.writeText('{{ $icalUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span x-text="copied ? 'Copié !' : 'Lien iCal'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 px-1 text-xs">
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-700"></div>
                <span class="text-gray-600 dark:text-gray-400">Disponible</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-amber-400"></div>
                <span class="text-gray-600 dark:text-gray-400">En attente</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-blue-500"></div>
                <span class="text-gray-600 dark:text-gray-400">Confirmée</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-violet-500"></div>
                <span class="text-gray-600 dark:text-gray-400">En cours</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-gray-800 dark:bg-gray-300"></div>
                <span class="text-gray-600 dark:text-gray-400">Bloqué</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-orange-400"></div>
                <span class="text-gray-600 dark:text-gray-400">Maintenance</span>
            </div>
            <span class="text-gray-400 dark:text-gray-500">|</span>
            <span class="text-gray-500 dark:text-gray-400 italic">Cliquez sur une date libre pour la bloquer</span>
        </div>

        {{-- Calendar Grid --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" style="min-width: {{ 180 + (count($days) * 36) }}px;">
                    <thead>
                        <tr>
                            <th class="sticky left-0 z-20 bg-gray-50 dark:bg-gray-900 border-b border-r border-gray-200 dark:border-gray-700 px-3 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider" style="min-width: 180px;">
                                Véhicule
                            </th>
                            @foreach($days as $day)
                                @php
                                    $isToday = $day->format('Y-m-d') === $today;
                                    $isWeekend = $day->isWeekend();
                                @endphp
                                <th class="border-b border-gray-200 dark:border-gray-700 px-0 py-1.5 text-center w-[34px] min-w-[34px]
                                    {{ $isToday ? 'bg-primary-50 dark:bg-primary-900/20' : ($isWeekend ? 'bg-gray-50/50 dark:bg-gray-900/30' : '') }}">
                                    <div class="text-[10px] font-medium {{ $isWeekend ? 'text-red-400' : 'text-gray-400 dark:text-gray-500' }}">
                                        {{ mb_substr($day->translatedFormat('D'), 0, 2) }}
                                    </div>
                                    <div class="text-xs font-bold {{ $isToday ? 'text-primary-600 dark:text-primary-400' : ($isWeekend ? 'text-red-500 dark:text-red-400' : 'text-gray-700 dark:text-gray-300') }}
                                        {{ $isToday ? 'bg-primary-600 dark:bg-primary-500 text-white rounded-full w-5 h-5 flex items-center justify-center mx-auto' : '' }}">
                                        {{ $day->format('d') }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr class="group" wire:key="vehicle-{{ $vehicle->id }}">
                                {{-- Vehicle info --}}
                                <td class="sticky left-0 z-10 bg-white dark:bg-gray-800 border-b border-r border-gray-200 dark:border-gray-700 px-3 py-2 group-hover:bg-gray-50 dark:group-hover:bg-gray-750 transition">
                                    <div class="flex items-center gap-2.5">
                                        @if($vehicle->image)
                                            <img src="{{ asset('storage/' . $vehicle->image) }}" alt="" class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-9 h-9 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H6.375c-.621 0-1.125-.504-1.125-1.125V11.25"/></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $vehicle->full_name }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA/j</span>
                                                <button
                                                    @click="openImport({{ $vehicle->id }}, '{{ addslashes($vehicle->full_name) }}')"
                                                    class="text-[10px] text-primary-600 dark:text-primary-400 hover:underline"
                                                    title="Importer Google Calendar"
                                                >
                                                    <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    iCal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Day cells --}}
                                @foreach($days as $day)
                                    @php
                                        $dateStr = $day->format('Y-m-d');
                                        $key = $vehicle->id . '_' . $dateStr;
                                        $booking = $bookingMap[$key] ?? null;
                                        $block = $blockMap[$key] ?? null;
                                        $isToday = $dateStr === $today;
                                        $isPast = $day->isPast() && !$isToday;
                                        $isWeekend = $day->isWeekend();

                                        if ($booking) {
                                            $cellClass = match($booking->status) {
                                                'pending' => 'bg-amber-400 dark:bg-amber-500',
                                                'confirmed' => 'bg-blue-500 dark:bg-blue-600',
                                                'active' => 'bg-violet-500 dark:bg-violet-600',
                                                default => 'bg-gray-400',
                                            };
                                            $cellType = 'booking';
                                        } elseif ($block) {
                                            $cellClass = match($block->type) {
                                                'blocked' => 'bg-gray-800 dark:bg-gray-300',
                                                'maintenance' => 'bg-orange-400 dark:bg-orange-500',
                                                default => 'bg-gray-400',
                                            };
                                            $cellType = $block->type;
                                        } else {
                                            $cellClass = $isPast
                                                ? 'bg-gray-50 dark:bg-gray-900/20'
                                                : 'bg-emerald-50 dark:bg-emerald-900/10 hover:bg-emerald-200 dark:hover:bg-emerald-800/30';
                                            $cellType = 'free';
                                        }
                                    @endphp
                                    <td
                                        class="border-b border-gray-100 dark:border-gray-700/50 p-0 text-center
                                            {{ $isToday ? 'ring-1 ring-inset ring-primary-400 dark:ring-primary-500' : '' }}"
                                        wire:key="cell-{{ $key }}"
                                    >
                                        @if($cellType === 'booking')
                                            <a
                                                href="{{ route('filament.loueur.resources.bookings.edit', $booking->id) }}"
                                                class="block w-full h-8 {{ $cellClass }} relative group/cell"
                                                title="{{ $booking->client_name }} ({{ $booking->status }})"
                                            >
                                                <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/cell:opacity-100 transition">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </span>
                                            </a>
                                        @elseif($cellType === 'blocked')
                                            <button
                                                wire:click="toggleBlock({{ $vehicle->id }}, '{{ $dateStr }}')"
                                                wire:loading.attr="disabled"
                                                class="block w-full h-8 {{ $cellClass }} cursor-pointer relative group/cell transition-all hover:opacity-80"
                                                title="Cliquez pour débloquer"
                                            >
                                                <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/cell:opacity-100 transition">
                                                    <svg class="w-3 h-3 text-white dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </span>
                                            </button>
                                        @elseif($cellType === 'maintenance')
                                            <div class="block w-full h-8 {{ $cellClass }}" title="Maintenance : {{ $block->reason ?? '' }}"></div>
                                        @elseif(!$isPast)
                                            <button
                                                wire:click="toggleBlock({{ $vehicle->id }}, '{{ $dateStr }}')"
                                                wire:loading.attr="disabled"
                                                class="block w-full h-8 {{ $cellClass }} cursor-pointer transition-all"
                                                title="Cliquez pour bloquer"
                                            ></button>
                                        @else
                                            <div class="block w-full h-8 {{ $cellClass }}"></div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($days) + 1 }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H6.375c-.621 0-1.125-.504-1.125-1.125V11.25"/></svg>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">Aucun véhicule actif</p>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">Ajoutez des véhicules pour voir le calendrier</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick tips --}}
        <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-xl p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                    <p class="font-medium">Astuce</p>
                    <p>Cliquez sur une case verte pour bloquer la date (le véhicule ne sera plus disponible). Cliquez sur une case noire pour la débloquer. Vous pouvez aussi importer un Google Calendar en cliquant sur le lien "iCal" sous chaque véhicule.</p>
                </div>
            </div>
        </div>

        {{-- Sync info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Synchronisation externe</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Abonnez Google Agenda, Apple Calendar ou Outlook à votre calendrier ResaDZ</p>
                </div>
                <div x-data="{ show: false }" class="relative flex-shrink-0">
                    <button @click="show = !show" class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        Voir le lien iCal
                    </button>
                    <div x-show="show" @click.outside="show = false" x-transition
                         class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 z-30">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Copiez ce lien dans votre application de calendrier :</p>
                        <div class="flex gap-2">
                            <input type="text" value="{{ $icalUrl }}" readonly class="flex-1 text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-gray-600 dark:text-gray-400" />
                            <button
                                @click="navigator.clipboard.writeText('{{ $icalUrl }}')"
                                class="px-2 py-1.5 text-xs bg-primary-600 text-white rounded hover:bg-primary-700 transition"
                            >Copier</button>
                        </div>
                        <div class="mt-2 text-[10px] text-gray-400 dark:text-gray-500 space-y-0.5">
                            <p><strong>Google Agenda :</strong> + > A partir de l'URL > Collez le lien</p>
                            <p><strong>Apple Calendar :</strong> Fichier > Nouvel abonnement > Collez le lien</p>
                            <p><strong>Outlook :</strong> Ajouter un calendrier > A partir d'Internet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Google Calendar Import Modal --}}
        <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @keydown.escape.window="showImportModal = false">
            <div @click.outside="showImportModal = false" x-transition x-show="showImportModal"
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Importer Google Calendar</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Véhicule : <strong x-text="importVehicleName" class="text-gray-900 dark:text-white"></strong>
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lien iCal (Google Calendar)</label>
                    <input
                        x-model="importUrl"
                        type="url"
                        placeholder="https://calendar.google.com/calendar/ical/..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm focus:ring-primary-500 focus:border-primary-500"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Dans Google Agenda : Paramètres du calendrier > Adresse publique au format iCal
                    </p>
                </div>

                <div class="flex gap-2 justify-end">
                    <button @click="showImportModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button
                        @click="if(importUrl && importVehicleId) { $wire.importGoogleCalendar(importVehicleId, importUrl); showImportModal = false; }"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition"
                    >
                        Importer
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 z-[60] items-center justify-center bg-black/20">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg px-6 py-4 flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mise à jour...</span>
        </div>
    </div>
</x-filament-panels::page>
