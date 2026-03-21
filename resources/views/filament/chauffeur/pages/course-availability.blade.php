<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="welcome-banner" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <x-heroicon-o-calendar-days class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Mes Disponibilites</h2>
                        <p class="text-white/80 text-sm">Gerez vos creneaux pour les transferts et livraisons</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="stat-card border-l-4 border-blue-500 bg-gradient-to-r from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-information-circle class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="font-semibold text-blue-800 dark:text-blue-200">Gerez vos disponibilites pour les courses</p>
                    <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
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
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-between">
                    <button wire:click="previousMonth" class="p-2 hover:bg-white/20 rounded-lg text-white transition-colors">
                        <x-heroicon-s-chevron-left class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-bold text-white capitalize">{{ $monthName }}</h3>
                        <button wire:click="goToToday" class="px-4 py-1.5 text-sm bg-white/20 backdrop-blur text-white rounded-lg hover:bg-white/30 font-medium transition-colors">
                            Aujourd'hui
                        </button>
                    </div>
                    <button wire:click="nextMonth" class="p-2 hover:bg-white/20 rounded-lg text-white transition-colors">
                        <x-heroicon-s-chevron-right class="w-5 h-5" />
                    </button>
                </div>

                {{-- Days of week header --}}
                <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
                        <div class="px-2 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">
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

                    {{-- Empty cells for padding --}}
                    @for($i = 0; $i < $startPadding; $i++)
                        <div class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"></div>
                    @endfor

                    {{-- Calendar days --}}
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
                                {{ $isPast ? 'bg-gray-50 dark:bg-gray-900 cursor-not-allowed opacity-50' : 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-inner' }}
                                {{ $status === 'available' ? 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20' : '' }}
                                {{ $status === 'unavailable' ? 'bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20' : '' }}"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold {{ $isToday ? 'w-8 h-8 flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full shadow-lg' : ($isPast ? 'text-gray-400' : 'text-gray-700 dark:text-gray-200') }}">
                                    {{ $day->format('j') }}
                                </span>
                                @if($status === 'available')
                                    <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow">
                                        <x-heroicon-s-check class="w-4 h-4 text-white" />
                                    </div>
                                @elseif($status === 'unavailable')
                                    <div class="w-6 h-6 bg-gradient-to-br from-red-500 to-rose-600 rounded-full flex items-center justify-center shadow">
                                        <x-heroicon-s-x-mark class="w-4 h-4 text-white" />
                                    </div>
                                @endif
                            </div>
                            @if($availability && isset($availability['items']))
                                <div class="mt-1 space-y-0.5">
                                    @foreach($availability['items'] as $item)
                                        <div class="text-xs font-medium truncate {{ $item->type === 'available' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
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

                    {{-- Padding for end of month --}}
                    @php
                        $totalCells = $startPadding + count($days);
                        $endPadding = $totalCells % 7 === 0 ? 0 : 7 - ($totalCells % 7);
                    @endphp
                    @for($i = 0; $i < $endPadding; $i++)
                        <div class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"></div>
                    @endfor
                </div>

                {{-- Legend --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex items-center justify-center gap-8">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow">
                            <x-heroicon-s-check class="w-3 h-3 text-white" />
                        </div>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Disponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-red-500 to-rose-600 rounded-full flex items-center justify-center shadow">
                            <x-heroicon-s-x-mark class="w-3 h-3 text-white" />
                        </div>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Indisponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Non defini</span>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Add availability form + recurring list --}}
            <div class="space-y-6">
                {{-- Add Availability Form --}}
                <div class="card-modern">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-o-plus class="w-5 h-5 text-white" />
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Ajouter une disponibilite</h4>
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
                    <div class="px-6 py-4 bg-gradient-to-r from-violet-500 to-purple-600">
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
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $recurring->time_slot_description }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $recurring->recurrence_description }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $recurring->services_description }}
                                        </p>
                                    </div>
                                    <button
                                        wire:click="deleteAvailability({{ $recurring->id }})"
                                        wire:confirm="Supprimer cette disponibilite recurrente ?"
                                        class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-xl flex items-center justify-center transition-colors"
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
                <div class="stat-card bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-light-bulb class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h5 class="font-bold text-amber-800 dark:text-amber-200">Conseils</h5>
                            <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1 mt-2 list-disc list-inside">
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
