<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Month Navigation --}}
        <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <a href="?month={{ $prevMonth->month }}&year={{ $prevMonth->year }}"
               class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Précédent
            </a>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white capitalize">{{ $monthName }}</h2>
            <a href="?month={{ $nextMonth->month }}&year={{ $nextMonth->year }}"
               class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                Suivant
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-gray-600 dark:text-gray-400">Disponible</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-amber-500 rounded"></div>
                <span class="text-gray-600 dark:text-gray-400">En attente</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span class="text-gray-600 dark:text-gray-400">Confirmée</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-500 rounded"></div>
                <span class="text-gray-600 dark:text-gray-400">En cours</span>
            </div>
        </div>

        {{-- Calendar Grid --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="sticky left-0 bg-gray-50 dark:bg-gray-700 px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-48 z-10">
                                Véhicule
                            </th>
                            @foreach($days as $day)
                                <th class="px-1 py-3 text-center text-xs font-medium {{ $day->isToday() ? 'bg-amber-100 dark:bg-amber-900/30' : '' }} {{ $day->isWeekend() ? 'text-red-500' : 'text-gray-600 dark:text-gray-300' }}">
                                    <div>{{ $day->format('D') }}</div>
                                    <div class="font-bold">{{ $day->format('d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($vehicles as $vehicle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="sticky left-0 bg-white dark:bg-gray-800 px-4 py-3 z-10">
                                    <div class="flex items-center gap-3">
                                        @if($vehicle->image)
                                            <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $vehicle->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA/j</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($days as $day)
                                    @php
                                        $key = $vehicle->id . '_' . $day->format('Y-m-d');
                                        $booking = $bookingMap[$key] ?? null;
                                        $statusColors = [
                                            'pending' => 'bg-amber-500',
                                            'confirmed' => 'bg-blue-500',
                                            'active' => 'bg-red-500',
                                        ];
                                        $bgColor = $booking ? ($statusColors[$booking->status] ?? 'bg-gray-400') : 'bg-green-500';
                                    @endphp
                                    <td class="px-1 py-3 text-center {{ $day->isToday() ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                        @if($booking)
                                            <a href="{{ route('filament.loueur.resources.bookings.edit', $booking->id) }}"
                                               class="block w-6 h-6 mx-auto rounded {{ $bgColor }} hover:ring-2 hover:ring-offset-1 hover:ring-gray-400 transition"
                                               title="{{ $booking->client_name }} - {{ $booking->status }}">
                                            </a>
                                        @else
                                            <div class="w-6 h-6 mx-auto rounded {{ $bgColor }} opacity-30"></div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($days) + 1 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Aucun véhicule actif. Ajoutez des véhicules pour voir le calendrier.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Upcoming Bookings --}}
        @if($bookings->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Réservations ce mois</h3>
                <div class="space-y-3">
                    @foreach($bookings->sortBy('start_date') as $booking)
                        <a href="{{ route('filament.loueur.resources.bookings.edit', $booking->id) }}"
                           class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <div class="flex items-center gap-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-500',
                                        'confirmed' => 'bg-blue-500',
                                        'active' => 'bg-red-500',
                                    ];
                                @endphp
                                <div class="w-3 h-3 rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-400' }}"></div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $booking->client_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->vehicle->full_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $booking->start_date->format('d/m') }} → {{ $booking->end_date->format('d/m') }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $booking->total_days }} jours</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
