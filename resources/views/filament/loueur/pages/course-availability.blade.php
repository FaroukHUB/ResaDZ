<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Card --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                <div>
                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">Gerez vos disponibilites pour les courses</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        Cliquez sur une date pour la marquer comme disponible. Cliquez a nouveau pour la marquer comme indisponible.
                        Utilisez le formulaire ci-dessous pour ajouter des disponibilites recurrentes.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calendar --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                {{-- Calendar Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <x-heroicon-s-chevron-left class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $monthName }}</h3>
                        <button wire:click="goToToday" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                            Aujourd'hui
                        </button>
                    </div>
                    <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <x-heroicon-s-chevron-right class="w-5 h-5" />
                    </button>
                </div>

                {{-- Days of week header --}}
                <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
                        <div class="px-2 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
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
                            class="min-h-[80px] p-2 border-b border-r border-gray-100 dark:border-gray-700 transition-colors
                                {{ $isPast ? 'bg-gray-50 dark:bg-gray-900 cursor-not-allowed' : 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700' }}
                                {{ $status === 'available' ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                                {{ $status === 'unavailable' ? 'bg-red-50 dark:bg-red-900/20' : '' }}"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium {{ $isToday ? 'w-7 h-7 flex items-center justify-center bg-blue-600 text-white rounded-full' : ($isPast ? 'text-gray-400' : 'text-gray-700 dark:text-gray-200') }}">
                                    {{ $day->format('j') }}
                                </span>
                                @if($status === 'available')
                                    <x-heroicon-s-check-circle class="w-4 h-4 text-green-600" />
                                @elseif($status === 'unavailable')
                                    <x-heroicon-s-x-circle class="w-4 h-4 text-red-600" />
                                @endif
                            </div>
                            @if($availability && isset($availability['items']))
                                <div class="mt-1 space-y-0.5">
                                    @foreach($availability['items'] as $item)
                                        <div class="text-xs truncate {{ $item->type === 'available' ? 'text-green-600' : 'text-red-600' }}">
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
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-100 dark:bg-green-900/30 border border-green-400 rounded"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Disponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-100 dark:bg-red-900/30 border border-red-400 rounded"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Indisponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-white dark:bg-gray-800 border border-gray-300 rounded"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Non defini</span>
                    </div>
                </div>
            </div>

            {{-- Sidebar: Add availability form + recurring list --}}
            <div class="space-y-6">
                {{-- Add Availability Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ajouter une disponibilite</h4>

                    <form wire:submit="createAvailability" class="space-y-4">
                        {{ $this->form }}

                        <x-filament::button type="submit" class="w-full">
                            Ajouter
                        </x-filament::button>
                    </form>
                </div>

                {{-- Recurring Availabilities --}}
                @if($recurringAvailabilities->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Disponibilites recurrentes</h4>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recurringAvailabilities as $recurring)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $recurring->type === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $recurring->type === 'available' ? 'Disponible' : 'Indisponible' }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
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
                                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Services Status --}}
                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Services actives</h5>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Transferts</span>
                            @if($offersTransfer)
                                <span class="inline-flex items-center gap-1 text-green-600">
                                    <x-heroicon-s-check-circle class="w-4 h-4" />
                                    <span class="text-xs">Active</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-gray-400">
                                    <x-heroicon-s-x-circle class="w-4 h-4" />
                                    <span class="text-xs">Inactive</span>
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Livraisons</span>
                            @if($offersDelivery)
                                <span class="inline-flex items-center gap-1 text-green-600">
                                    <x-heroicon-s-check-circle class="w-4 h-4" />
                                    <span class="text-xs">Active</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-gray-400">
                                    <x-heroicon-s-x-circle class="w-4 h-4" />
                                    <span class="text-xs">Inactive</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
