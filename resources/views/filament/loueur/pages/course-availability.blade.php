<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Hero Card --}}
        <div class="ch-hero">
            <div class="ch-hero-icon">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
            <div>
                <h2>Gérez vos disponibilités 📅</h2>
                <p>Cliquez sur une date pour la marquer disponible ou indisponible. Vos clients verront vos créneaux en temps réel.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Calendar --}}
            <div class="lg:col-span-2 ch-calendar">
                {{-- Calendar Header --}}
                <div class="ch-calendar-header">
                    <button wire:click="previousMonth" class="ch-calendar-nav">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-bold capitalize">{{ $monthName }}</h3>
                        <button wire:click="goToToday" class="px-3 py-1 text-sm bg-white/15 rounded-lg hover:bg-white/25 transition-all">
                            Aujourd'hui
                        </button>
                    </div>
                    <button wire:click="nextMonth" class="ch-calendar-nav">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>

                {{-- Days of week header --}}
                <div class="grid grid-cols-7 border-b border-gray-100">
                    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
                        <div class="px-2 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            {{ $dayName }}
                        </div>
                    @endforeach
                </div>

                {{-- Calendar Grid --}}
                <div class="grid grid-cols-7">
                    @php
                        $firstDay = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                        $startPadding = ($firstDay->dayOfWeekIso - 1);
                    @endphp

                    @for($i = 0; $i < $startPadding; $i++)
                        <div class="ch-calendar-day ch-day-past" style="background: #FAFBFE; cursor: default;"></div>
                    @endfor

                    @foreach($days as $day)
                        @php
                            $dateKey = $day->format('Y-m-d');
                            $isToday = $dateKey === $today;
                            $isPast = $day->lt(now()->startOfDay());
                            $availability = $availabilityMap[$dateKey] ?? null;
                            $status = $availability['status'] ?? 'none';
                        @endphp
                        <div
                            wire:click="{{ !$isPast ? 'quickAddAvailability(\'' . $dateKey . '\')' : '' }}"
                            class="ch-calendar-day {{ $isPast ? 'ch-day-past' : '' }}
                                {{ $status === 'available' ? 'ch-day-available' : '' }}
                                {{ $status === 'unavailable' ? 'ch-day-unavailable' : '' }}
                                {{ $isToday ? 'ch-day-today' : '' }}"
                        >
                            <div class="flex items-center justify-between">
                                @if($isToday)
                                    <span class="ch-day-number">{{ $day->format('j') }}</span>
                                @else
                                    <span class="text-sm font-medium {{ $isPast ? 'text-gray-300' : 'text-gray-700' }}">
                                        {{ $day->format('j') }}
                                    </span>
                                @endif

                                @if($status !== 'none')
                                    <div class="ch-day-dot"></div>
                                @endif
                            </div>

                            @if($availability && isset($availability['items']))
                                <div class="mt-1 space-y-0.5">
                                    @foreach($availability['items'] as $item)
                                        <div class="text-[10px] truncate font-medium {{ $item->type === 'available' ? 'text-indigo-600' : 'text-red-500' }}">
                                            @if($item->start_time)
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                            @else
                                                Journée
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @php
                        $totalCells = $startPadding + count($days);
                        $endPadding = $totalCells % 7 === 0 ? 0 : 7 - ($totalCells % 7);
                    @endphp
                    @for($i = 0; $i < $endPadding; $i++)
                        <div class="ch-calendar-day ch-day-past" style="background: #FAFBFE; cursor: default;"></div>
                    @endfor
                </div>

                {{-- Legend --}}
                <div class="ch-legend">
                    <div class="ch-legend-item">
                        <div class="ch-legend-dot" style="background: #6366F1;"></div>
                        Disponible
                    </div>
                    <div class="ch-legend-item">
                        <div class="ch-legend-dot" style="background: #EF4444;"></div>
                        Indisponible
                    </div>
                    <div class="ch-legend-item">
                        <div class="ch-legend-dot" style="background: #F59E0B;"></div>
                        Partiel
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Add Availability Form --}}
                <div style="background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); overflow: hidden;">
                    <div style="background: linear-gradient(135deg, #6366F1, #818CF8); padding: 1rem 1.25rem;">
                        <h4 class="text-white font-bold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Ajouter une disponibilité
                        </h4>
                    </div>
                    <div class="p-5">
                        <form wire:submit="createAvailability" class="space-y-4">
                            {{ $this->form }}
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all" style="background: linear-gradient(135deg, #6366F1, #818CF8); box-shadow: 0 4px 14px rgba(99,102,241,0.3);">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                Enregistrer
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Recurring Availabilities --}}
                @if($recurringAvailabilities->count() > 0)
                <div style="background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); overflow: hidden;">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h4 class="font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5" style="color: #6366F1;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" /></svg>
                            Récurrences
                        </h4>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($recurringAvailabilities as $recurring)
                            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="ch-badge {{ $recurring->type === 'available' ? 'ch-badge-confirmed' : 'ch-badge-cancelled' }}">
                                            {{ $recurring->type === 'available' ? 'Dispo' : 'Indispo' }}
                                        </span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $recurring->time_slot_description }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ $recurring->recurrence_description }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $recurring->services_description }}</p>
                                </div>
                                <button
                                    wire:click="deleteAvailability({{ $recurring->id }})"
                                    wire:confirm="Supprimer cette disponibilité récurrente ?"
                                    class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Services Status --}}
                <div style="background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); padding: 1.25rem;">
                    <h5 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" style="color: #6366F1;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Services activés
                    </h5>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between p-2.5 rounded-lg" style="{{ $offersTransfer ? 'background: #EEF2FF;' : 'background: #F8FAFC;' }}">
                            <span class="text-sm font-medium {{ $offersTransfer ? 'text-indigo-700' : 'text-gray-400' }}">✈️ Transferts</span>
                            @if($offersTransfer)
                                <span class="ch-badge ch-badge-confirmed">Actif</span>
                            @else
                                <span class="ch-badge" style="background: #F1F5F9; color: #94A3B8;">Inactif</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-lg" style="{{ $offersDelivery ? 'background: #ECFDF5;' : 'background: #F8FAFC;' }}">
                            <span class="text-sm font-medium {{ $offersDelivery ? 'text-emerald-700' : 'text-gray-400' }}">📦 Livraisons</span>
                            @if($offersDelivery)
                                <span class="ch-badge ch-badge-confirmed">Actif</span>
                            @else
                                <span class="ch-badge" style="background: #F1F5F9; color: #94A3B8;">Inactif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
