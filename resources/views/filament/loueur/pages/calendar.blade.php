<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Navigation et filtres --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <x-filament::button
                    wire:click="previousMonth"
                    icon="heroicon-o-chevron-left"
                    color="gray"
                    size="sm"
                />
                <h2 class="text-xl font-semibold min-w-[200px] text-center">
                    {{ $calendarData['month'] ?? '' }}
                </h2>
                <x-filament::button
                    wire:click="nextMonth"
                    icon="heroicon-o-chevron-right"
                    color="gray"
                    size="sm"
                />
                <x-filament::button
                    wire:click="goToToday"
                    color="gray"
                    size="sm"
                >
                    Aujourd'hui
                </x-filament::button>
            </div>

            {{-- Filtre véhicule --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Véhicule :</span>
                <select
                    wire:change="selectVehicle($event.target.value)"
                    class="border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    <option value="">Tous les véhicules</option>
                    @foreach($calendarData['vehicles'] ?? [] as $vehicle)
                        <option value="{{ $vehicle['id'] }}" @selected($selectedVehicleId == $vehicle['id'])>
                            {{ $vehicle['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-amber-500"></span>
                <span>En attente</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-blue-500"></span>
                <span>Confirmée</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-green-500"></span>
                <span>En cours</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-red-500"></span>
                <span>Bloqué</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-orange-500"></span>
                <span>Maintenance</span>
            </div>
        </div>

        {{-- Calendrier --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            {{-- Jours de la semaine --}}
            <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700 border-b">
                @foreach(['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] as $dayName)
                    <div class="p-2 text-center text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            {{-- Grille du calendrier --}}
            @php
                $days = $calendarData['days'] ?? [];
                $events = $calendarData['events'] ?? [];
                $firstDay = !empty($days) ? \Carbon\Carbon::parse($days[0]['date'])->dayOfWeek : 0;
            @endphp

            <div class="grid grid-cols-7">
                {{-- Cellules vides avant le premier jour --}}
                @for($i = 0; $i < $firstDay; $i++)
                    <div class="min-h-[100px] border-r border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"></div>
                @endfor

                {{-- Jours du mois --}}
                @foreach($days as $day)
                    @php
                        $dayEvents = collect($events)->filter(function($event) use ($day) {
                            return $day['date'] >= $event['startDate'] && $day['date'] <= $event['endDate'];
                        });
                    @endphp

                    <div class="min-h-[100px] border-r border-b border-gray-100 dark:border-gray-700 p-1 {{ $day['isToday'] ? 'bg-primary-50 dark:bg-primary-900/20' : ($day['isWeekend'] ? 'bg-gray-50 dark:bg-gray-900/50' : '') }}">
                        <div class="text-right mb-1">
                            <span class="inline-flex items-center justify-center w-7 h-7 text-sm {{ $day['isToday'] ? 'bg-primary-500 text-white rounded-full font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $day['day'] }}
                            </span>
                        </div>

                        <div class="space-y-1 overflow-y-auto max-h-[70px]">
                            @foreach($dayEvents as $event)
                                @php
                                    $bgColor = match($event['color']) {
                                        'amber' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 border-amber-300',
                                        'blue' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 border-blue-300',
                                        'green' => 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 border-green-300',
                                        'red' => 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 border-red-300',
                                        'orange' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-800 dark:text-orange-200 border-orange-300',
                                        default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300',
                                    };
                                @endphp

                                <div
                                    class="text-xs px-1 py-0.5 rounded border truncate cursor-pointer hover:opacity-80 {{ $bgColor }}"
                                    title="{{ $event['vehicleName'] }}: {{ $event['title'] }} ({{ $event['startDate'] }} - {{ $event['endDate'] }})"
                                    @if($event['type'] === 'availability')
                                        wire:click="deleteAvailability({{ explode('-', $event['id'])[1] }})"
                                        wire:confirm="Supprimer ce blocage ?"
                                    @endif
                                >
                                    {{ Str::limit($event['title'], 15) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Cellules vides après le dernier jour --}}
                @php
                    $lastDay = !empty($days) ? \Carbon\Carbon::parse(end($days)['date'])->dayOfWeek : 6;
                    $remainingCells = 6 - $lastDay;
                @endphp
                @for($i = 0; $i < $remainingCells; $i++)
                    <div class="min-h-[100px] border-r border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"></div>
                @endfor
            </div>
        </div>

        {{-- Formulaire de blocage --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Bloquer des dates</h3>
            <form wire:submit="blockDates">
                {{ $this->blockDatesForm }}

                <div class="mt-4">
                    <x-filament::button type="submit">
                        Bloquer ces dates
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Liste des événements du mois --}}
        @if(!empty($calendarData['events']))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Événements du mois</h3>
                <div class="space-y-2">
                    @foreach(collect($calendarData['events'])->sortBy('startDate') as $event)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                @php
                                    $dotColor = match($event['color']) {
                                        'amber' => 'bg-amber-500',
                                        'blue' => 'bg-blue-500',
                                        'green' => 'bg-green-500',
                                        'red' => 'bg-red-500',
                                        'orange' => 'bg-orange-500',
                                        default => 'bg-gray-500',
                                    };
                                @endphp
                                <span class="w-3 h-3 rounded-full {{ $dotColor }}"></span>
                                <div>
                                    <p class="font-medium">{{ $event['title'] }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $event['vehicleName'] }} &bull;
                                        {{ \Carbon\Carbon::parse($event['startDate'])->format('d/m') }} - {{ \Carbon\Carbon::parse($event['endDate'])->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            @if($event['type'] === 'availability')
                                <x-filament::button
                                    color="danger"
                                    size="xs"
                                    wire:click="deleteAvailability({{ explode('-', $event['id'])[1] }})"
                                    wire:confirm="Supprimer ce blocage ?"
                                >
                                    Supprimer
                                </x-filament::button>
                            @else
                                <span class="text-sm text-gray-500">
                                    {{ match($event['status'] ?? '') {
                                        'pending' => 'En attente',
                                        'confirmed' => 'Confirmée',
                                        'active' => 'En cours',
                                        default => '',
                                    } }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
