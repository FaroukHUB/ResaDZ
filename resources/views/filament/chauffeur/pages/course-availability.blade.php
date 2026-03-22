<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="hero-card" style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%);">
            <div class="relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Mes Disponibilites</h2>
                        <p class="text-white/80 text-sm mt-1">Gerez vos creneaux pour les transferts et livraisons</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="stat-card border-l-4 border-[#FF6B2C] bg-gradient-to-r from-orange-50 to-white dark:from-orange-900/10 dark:to-gray-800">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B] rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">Gerez vos disponibilites pour les courses</p>
                    <p class="text-sm text-[#64748B] mt-1">
                        Cliquez sur une date pour la marquer comme disponible. Cliquez a nouveau pour la marquer comme indisponible.
                        Utilisez le formulaire ci-dessous pour ajouter des disponibilites recurrentes avec des creneaux horaires.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calendar --}}
            <div class="lg:col-span-2 card-modern overflow-hidden p-0">
                {{-- Calendar Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-[#FF6B2C] to-[#F59E0B] flex items-center justify-between">
                    <button wire:click="previousMonth" class="p-2 hover:bg-white/20 rounded-xl text-white transition-all">
                        <x-heroicon-s-chevron-left class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-bold text-white capitalize">{{ $monthName }}</h3>
                        <button wire:click="goToToday" class="px-4 py-1.5 text-sm bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 font-semibold transition-all">
                            Aujourd'hui
                        </button>
                    </div>
                    <button wire:click="nextMonth" class="p-2 hover:bg-white/20 rounded-xl text-white transition-all">
                        <x-heroicon-s-chevron-right class="w-5 h-5" />
                    </button>
                </div>

                {{-- Days of week header --}}
                <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-[#F8FAFF] dark:bg-gray-900">
                    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
                        <div class="px-2 py-3 text-center text-xs font-bold text-[#64748B] uppercase tracking-wider">
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
                        <div class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900"></div>
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
                            class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 transition-all duration-200
                                {{ $isPast ? 'bg-gray-50 dark:bg-gray-900 cursor-not-allowed opacity-50' : 'cursor-pointer hover:bg-orange-50/50 dark:hover:bg-gray-700 hover:shadow-inner' }}
                                {{ $status === 'available' ? 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20' : '' }}
                                {{ $status === 'unavailable' ? 'bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20' : '' }}"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold {{ $isToday ? 'w-8 h-8 flex items-center justify-center bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B] text-white rounded-full shadow-lg' : ($isPast ? 'text-gray-400' : 'text-[#1E293B] dark:text-gray-200') }}">
                                    {{ $day->format('j') }}
                                </span>
                                @if($status === 'available')
                                    <div class="w-6 h-6 bg-gradient-to-br from-[#10B981] to-emerald-600 rounded-full flex items-center justify-center shadow">
                                        <x-heroicon-s-check class="w-4 h-4 text-white" />
                                    </div>
                                @elseif($status === 'unavailable')
                                    <div class="w-6 h-6 bg-gradient-to-br from-[#EF4444] to-rose-600 rounded-full flex items-center justify-center shadow">
                                        <x-heroicon-s-x-mark class="w-4 h-4 text-white" />
                                    </div>
                                @endif
                            </div>
                            @if($availability && isset($availability['items']))
                                <div class="mt-1 space-y-0.5">
                                    @foreach($availability['items'] as $item)
                                        <div class="text-xs font-medium truncate {{ $item->type === 'available' ? 'text-[#10B981]' : 'text-[#EF4444]' }}">
                                            @if($item->start_time)
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                            @else
                                                Journee
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
                        <div class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900"></div>
                    @endfor
                </div>

                {{-- Legend --}}
                <div class="px-6 py-4 bg-[#F8FAFF] dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex items-center justify-center gap-8">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-[#10B981] to-emerald-600 rounded-full flex items-center justify-center shadow">
                            <x-heroicon-s-check class="w-3 h-3 text-white" />
                        </div>
                        <span class="text-sm font-medium text-[#64748B]">Disponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-[#EF4444] to-rose-600 rounded-full flex items-center justify-center shadow">
                            <x-heroicon-s-x-mark class="w-3 h-3 text-white" />
                        </div>
                        <span class="text-sm font-medium text-[#64748B]">Indisponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-full"></div>
                        <span class="text-sm font-medium text-[#64748B]">Non defini</span>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Add Availability Form --}}
                <div class="card-modern">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B] rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-o-plus class="w-5 h-5 text-white" />
                        </div>
                        <h4 class="text-lg font-bold text-[#1E293B] dark:text-white">Ajouter une disponibilite</h4>
                    </div>

                    <form wire:submit="createAvailability" class="space-y-4">
                        {{ $this->form }}

                        <x-filament::button type="submit" class="w-full btn-gradient-primary">
                            <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                            Ajouter
                        </x-filament::button>
                    </form>
                </div>

                {{-- Recurring Availabilities --}}
                @if($recurringAvailabilities->count() > 0)
                <div class="card-modern overflow-hidden p-0">
                    <div class="px-6 py-4 bg-gradient-to-r from-[#6366F1] to-[#8B5CF6]">
                        <h4 class="text-lg font-bold text-white flex items-center gap-2">
                            <x-heroicon-o-arrow-path class="w-5 h-5" />
                            Disponibilites recurrentes
                        </h4>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recurringAvailabilities as $recurring)
                            <div class="data-row px-6 py-4">
                                <div class="flex items-center justify-between w-full">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="badge-modern {{ $recurring->type === 'available' ? 'badge-success' : 'badge-danger' }}">
                                                {{ $recurring->type === 'available' ? 'Disponible' : 'Indisponible' }}
                                            </span>
                                            <span class="text-sm font-semibold text-[#1E293B] dark:text-white">
                                                {{ $recurring->time_slot_description }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#64748B] mt-1">
                                            {{ $recurring->recurrence_description }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $recurring->services_description }}
                                        </p>
                                    </div>
                                    <button
                                        wire:click="deleteAvailability({{ $recurring->id }})"
                                        wire:confirm="Supprimer cette disponibilite recurrente ?"
                                        class="w-10 h-10 bg-red-50 dark:bg-red-900/30 text-[#EF4444] hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl flex items-center justify-center transition-all"
                                    >
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Quick Tips --}}
                <div class="stat-card bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/10 dark:to-amber-900/10 border border-orange-200 dark:border-orange-800">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#FF6B2C] to-[#F59E0B] rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-[#1E293B] dark:text-white">Conseils</h5>
                            <ul class="text-sm text-[#64748B] space-y-1.5 mt-2 list-disc list-inside">
                                <li>Definissez vos horaires habituels avec une recurrence hebdomadaire</li>
                                <li>Marquez les jours de conge comme indisponibles</li>
                                <li>Les clients verront uniquement vos creneaux disponibles</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
