<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Today Visits --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Visites aujourd'hui</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($todayVisits) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $todayUnique }} visiteurs uniques</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
                @if($yesterdayVisits > 0)
                    @php
                        $change = (($todayVisits - $yesterdayVisits) / $yesterdayVisits) * 100;
                    @endphp
                    <div class="mt-2 text-sm {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs hier
                    </div>
                @endif
            </div>

            {{-- Month Visits --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Visites ce mois</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($monthVisits) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $monthUnique }} visiteurs uniques</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                @if($lastMonthVisits > 0)
                    @php
                        $change = (($monthVisits - $lastMonthVisits) / $lastMonthVisits) * 100;
                    @endphp
                    <div class="mt-2 text-sm {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs mois dernier
                    </div>
                @endif
            </div>

            {{-- Month Bookings --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Réservations ce mois</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($monthBookings) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Active --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Plateforme</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalLoueurs }} loueurs</p>
                        <p class="text-sm text-gray-500">{{ $totalVehicles }} véhicules actifs</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Pages --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pages les plus visitées</h3>
                <div class="space-y-3">
                    @php
                        $pageLabels = [
                            'home' => 'Accueil',
                            'vehicle' => 'Détail véhicule',
                            'vehicles_list' => 'Liste véhicules',
                            'loueur' => 'Page loueur',
                            'booking' => 'Réservation',
                            'seo_wilaya' => 'Pages SEO wilaya',
                            'compare' => 'Comparateur',
                            'review' => 'Avis',
                            'other' => 'Autres',
                        ];
                        $totalPageVisits = $topPages->sum('visits');
                    @endphp
                    @forelse($topPages as $page)
                        @php
                            $percentage = $totalPageVisits > 0 ? ($page->visits / $totalPageVisits) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">{{ $pageLabels[$page->page_type] ?? $page->page_type }}</span>
                                <span class="text-gray-500">{{ number_format($page->visits) }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>

            {{-- Device Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appareils</h3>
                <div class="grid grid-cols-3 gap-4">
                    @php
                        $deviceIcons = [
                            'mobile' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                            'tablet' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                            'desktop' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                        ];
                        $deviceLabels = ['mobile' => 'Mobile', 'tablet' => 'Tablette', 'desktop' => 'Ordinateur'];
                        $totalDevices = $deviceStats->sum('visits');
                    @endphp
                    @foreach(['mobile', 'desktop', 'tablet'] as $device)
                        @php
                            $stat = $deviceStats->firstWhere('device_type', $device);
                            $visits = $stat->visits ?? 0;
                            $percentage = $totalDevices > 0 ? ($visits / $totalDevices) * 100 : 0;
                        @endphp
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="text-gray-400 mb-2 flex justify-center">{!! $deviceIcons[$device] !!}</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($percentage, 0) }}%</div>
                            <div class="text-xs text-gray-500">{{ $deviceLabels[$device] }}</div>
                        </div>
                    @endforeach
                </div>

                <h4 class="text-md font-semibold text-gray-900 dark:text-white mt-6 mb-3">Navigateurs</h4>
                <div class="space-y-2">
                    @forelse($browserStats as $browser)
                        @php
                            $percentage = $monthVisits > 0 ? ($browser->visits / $monthVisits) * 100 : 0;
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $browser->browser ?? 'Unknown' }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($percentage, 1) }}%</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Pas encore de données</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Top Vehicles --}}
        @if($topVehicles->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Véhicules les plus consultés</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach($topVehicles as $item)
                        @if($item->vehicle)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                @if($item->vehicle->image)
                                    <img src="{{ asset('storage/' . $item->vehicle->image) }}" alt="{{ $item->vehicle->full_name }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg"></div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $item->vehicle->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format($item->visits) }} vues</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
