<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Real-time Visitors Banner --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-3 h-3 bg-white rounded-full animate-ping absolute"></div>
                        <div class="w-3 h-3 bg-white rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-white text-lg font-bold">{{ $realtimeVisitors }} visiteur{{ $realtimeVisitors > 1 ? 's' : '' }} en ligne</p>
                        <p class="text-green-100 text-sm">Actifs ces 5 dernières minutes</p>
                    </div>
                </div>
                <div class="text-white/80">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Votre IP actuelle :</strong> <code class="bg-blue-100 dark:bg-blue-800 px-2 py-0.5 rounded">{{ $currentIp }}</code>
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                        Pour exclure votre IP du tracking, ajoutez dans votre fichier <code>.env</code> :
                        <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">EXCLUDED_TRACKING_IPS={{ $currentIp }}</code>
                    </p>
                    @if(count($excludedIps) > 0)
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            IPs actuellement exclues : {{ implode(', ', $excludedIps) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Stats Row 1 --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            {{-- Today Visits --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($todayVisits) }}</p>
                        <p class="text-xs text-gray-500">Aujourd'hui</p>
                    </div>
                </div>
                @if($yesterdayVisits > 0)
                    @php $change = (($todayVisits - $yesterdayVisits) / $yesterdayVisits) * 100; @endphp
                    <div class="mt-2 text-xs {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs hier
                    </div>
                @endif
            </div>

            {{-- Yesterday --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($yesterdayVisits) }}</p>
                        <p class="text-xs text-gray-500">Hier</p>
                    </div>
                </div>
            </div>

            {{-- This Week --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($weekVisits) }}</p>
                        <p class="text-xs text-gray-500">Cette semaine</p>
                    </div>
                </div>
            </div>

            {{-- This Month --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($monthVisits) }}</p>
                        <p class="text-xs text-gray-500">Ce mois</p>
                    </div>
                </div>
                @if($lastMonthVisits > 0)
                    @php $change = (($monthVisits - $lastMonthVisits) / $lastMonthVisits) * 100; @endphp
                    <div class="mt-2 text-xs {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs mois dernier
                    </div>
                @endif
            </div>

            {{-- Unique Visitors --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($monthUnique) }}</p>
                        <p class="text-xs text-gray-500">Uniques (mois)</p>
                    </div>
                </div>
            </div>

            {{-- Bounce Rate --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bounceRate }}%</p>
                        <p class="text-xs text-gray-500">Taux rebond</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Business Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm">Réservations ce mois</p>
                        <p class="text-3xl font-bold">{{ number_format($monthBookings) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm">Revenus ce mois</p>
                        <p class="text-3xl font-bold">{{ number_format($monthRevenue) }} DA</p>
                    </div>
                    <svg class="w-10 h-10 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Loueurs actifs</p>
                        <p class="text-3xl font-bold">{{ number_format($totalLoueurs) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl shadow p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Véhicules actifs</p>
                        <p class="text-3xl font-bold">{{ number_format($totalVehicles) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Conversion Funnel --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Entonnoir de conversion (ce mois)
            </h3>
            <div class="flex items-center justify-between gap-2">
                @php
                    $funnelSteps = [
                        ['name' => 'Accueil', 'value' => $funnelHome, 'color' => 'blue'],
                        ['name' => 'Liste véhicules', 'value' => $funnelVehicleList, 'color' => 'indigo'],
                        ['name' => 'Détail véhicule', 'value' => $funnelVehicle, 'color' => 'purple'],
                        ['name' => 'Page réservation', 'value' => $funnelBooking, 'color' => 'amber'],
                        ['name' => 'Réservation complétée', 'value' => $funnelCompleted, 'color' => 'green'],
                    ];
                    $maxValue = max($funnelHome, 1);
                @endphp
                @foreach($funnelSteps as $index => $step)
                    <div class="flex-1 text-center">
                        <div class="relative mx-auto" style="width: {{ max(60, 100 - ($index * 8)) }}%">
                            <div class="bg-{{ $step['color'] }}-100 dark:bg-{{ $step['color'] }}-900/30 rounded-lg p-3 border-2 border-{{ $step['color'] }}-300 dark:border-{{ $step['color'] }}-700">
                                <p class="text-2xl font-bold text-{{ $step['color'] }}-600 dark:text-{{ $step['color'] }}-400">{{ number_format($step['value']) }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $step['name'] }}</p>
                            </div>
                            @if($index > 0 && $funnelSteps[$index - 1]['value'] > 0)
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ number_format(($step['value'] / $funnelSteps[$index - 1]['value']) * 100, 1) }}%
                                </p>
                            @endif
                        </div>
                        @if($index < count($funnelSteps) - 1)
                            <div class="hidden md:block absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Geographic Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Top Countries --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pays (ce mois)
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($topCountries as $country)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="flex items-center gap-2">
                                @if($country->country_code)
                                    <img src="https://flagcdn.com/24x18/{{ strtolower($country->country_code) }}.png" alt="" class="w-6 h-4 rounded">
                                @endif
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $country->country ?? 'Inconnu' }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($country->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>

            {{-- Top Cities --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Villes (ce mois)
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($topCities as $city)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="flex items-center gap-2">
                                @if($city->country_code)
                                    <img src="https://flagcdn.com/16x12/{{ strtolower($city->country_code) }}.png" alt="" class="w-4 h-3 rounded">
                                @endif
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $city->city ?? 'Inconnu' }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($city->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>

            {{-- Top Regions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Régions (ce mois)
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($topRegions as $region)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="flex items-center gap-2">
                                @if($region->country_code)
                                    <img src="https://flagcdn.com/16x12/{{ strtolower($region->country_code) }}.png" alt="" class="w-4 h-3 rounded">
                                @endif
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $region->region ?? 'Inconnu' }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($region->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Traffic Sources --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Traffic by Medium --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Canaux de trafic
                </h3>
                @php
                    $mediumColors = ['direct' => 'blue', 'organic' => 'green', 'social' => 'pink', 'referral' => 'purple', 'email' => 'yellow', 'campaign' => 'orange'];
                    $mediumLabels = ['direct' => 'Direct', 'organic' => 'Recherche', 'social' => 'Réseaux sociaux', 'referral' => 'Référence', 'email' => 'Email', 'campaign' => 'Campagne'];
                    $totalMedium = array_sum(array_column($trafficByMedium, 'visits'));
                @endphp
                <div class="space-y-3">
                    @forelse($trafficByMedium as $medium)
                        @php
                            $percentage = $totalMedium > 0 ? ($medium->visits / $totalMedium) * 100 : 0;
                            $color = $mediumColors[$medium->traffic_medium] ?? 'gray';
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">{{ $mediumLabels[$medium->traffic_medium] ?? ucfirst($medium->traffic_medium ?? 'Inconnu') }}</span>
                                <span class="text-gray-500">{{ number_format($medium->visits) }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-{{ $color }}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>

            {{-- Traffic Sources Detail --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Sources de trafic
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($trafficSources as $source)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $source->traffic_source ?? 'Direct' }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $source->traffic_medium ?? '-' }})</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($source->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- UTM Campaigns --}}
        @if(count($utmCampaigns) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Campagnes UTM (ce mois)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Source</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Medium</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Campagne</th>
                            <th class="text-right py-2 px-3 text-gray-500 font-medium">Visites</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($utmCampaigns as $campaign)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-2 px-3 text-gray-700 dark:text-gray-300">{{ $campaign->utm_source }}</td>
                                <td class="py-2 px-3 text-gray-700 dark:text-gray-300">{{ $campaign->utm_medium }}</td>
                                <td class="py-2 px-3 text-gray-900 dark:text-white font-medium">{{ $campaign->utm_campaign }}</td>
                                <td class="py-2 px-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($campaign->visits) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Time-based Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Hourly Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Visites par heure (7 derniers jours)
                </h3>
                <div class="flex items-end justify-between h-32 gap-1">
                    @php $maxHourly = max($hourlyStats) ?: 1; @endphp
                    @for($i = 0; $i < 24; $i++)
                        @php $value = $hourlyStats[$i] ?? 0; $height = ($value / $maxHourly) * 100; @endphp
                        <div class="flex-1 flex flex-col items-center group relative">
                            <div class="w-full bg-blue-500 rounded-t transition-all hover:bg-blue-600" style="height: {{ max(2, $height) }}%"></div>
                            <span class="text-xs text-gray-400 mt-1">{{ $i }}</span>
                            <div class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap">{{ $value }} visites</div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Day of Week Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Visites par jour (ce mois)
                </h3>
                <div class="space-y-2">
                    @php $maxDay = max($dayOfWeekStats ?: [1]) ?: 1; @endphp
                    @foreach($dayNames as $dayNum => $dayName)
                        @php $value = $dayOfWeekStats[$dayNum] ?? 0; $percentage = ($value / $maxDay) * 100; @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-20 text-sm text-gray-600 dark:text-gray-400">{{ $dayName }}</span>
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-indigo-500 h-4 rounded-full flex items-center justify-end pr-2" style="width: {{ max(5, $percentage) }}%">
                                    <span class="text-xs text-white font-medium">{{ $value }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Daily Visits --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Visites quotidiennes (30 derniers jours)
            </h3>
            <div class="flex items-end justify-between h-40 gap-1">
                @php $maxDaily = max(array_column($dailyVisits, 'visits')) ?: 1; @endphp
                @foreach($dailyVisits as $day)
                    @php $height = ($day->visits / $maxDaily) * 100; @endphp
                    <div class="flex-1 flex flex-col items-center group relative">
                        <div class="w-full bg-green-500 rounded-t transition-all hover:bg-green-600" style="height: {{ max(2, $height) }}%"></div>
                        <div class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10">
                            {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}: {{ $day->visits }} visites
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between text-xs text-gray-400 mt-2">
                <span>{{ !empty($dailyVisits) ? \Carbon\Carbon::parse($dailyVisits[0]->date)->format('d M') : '' }}</span>
                <span>{{ !empty($dailyVisits) ? \Carbon\Carbon::parse(end($dailyVisits)->date)->format('d M') : '' }}</span>
            </div>
        </div>

        {{-- Click Events Stats --}}
        @if($clickStats && count($clickStats) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                </svg>
                Clics sur les actions (ce mois)
            </h3>
            @php
                $clickLabels = [
                    'phone' => ['label' => 'Appels téléphone', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'color' => 'green'],
                    'whatsapp' => ['label' => 'Messages WhatsApp', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'color' => 'emerald'],
                    'reserve' => ['label' => 'Réservations', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                    'view_details' => ['label' => 'Voir détails', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'blue'],
                    'share' => ['label' => 'Partages', 'icon' => 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z', 'color' => 'purple'],
                ];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($clickStats as $click)
                    @php $config = $clickLabels[$click->event_type] ?? ['label' => ucfirst($click->event_type), 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'gray']; @endphp
                    <div class="text-center p-4 bg-{{ $config['color'] }}-50 dark:bg-{{ $config['color'] }}-900/20 rounded-xl">
                        <div class="w-10 h-10 mx-auto mb-2 bg-{{ $config['color'] }}-100 dark:bg-{{ $config['color'] }}-800 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($click->clicks) }}</p>
                        <p class="text-xs text-gray-500">{{ $config['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Device, Browser, OS Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Devices --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appareils</h3>
                @php
                    $deviceIcons = [
                        'mobile' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                        'tablet' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                        'desktop' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                    ];
                    $deviceLabels = ['mobile' => 'Mobile', 'tablet' => 'Tablette', 'desktop' => 'Ordinateur'];
                    $totalDevices = array_sum(array_column($deviceStats, 'visits'));
                @endphp
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['mobile', 'desktop', 'tablet'] as $device)
                        @php
                            $stat = null;
                            foreach ($deviceStats as $d) { if ($d->device_type === $device) { $stat = $d; break; } }
                            $visits = $stat->visits ?? 0;
                            $percentage = $totalDevices > 0 ? ($visits / $totalDevices) * 100 : 0;
                        @endphp
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="text-gray-400 mb-1 flex justify-center">{!! $deviceIcons[$device] !!}</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($percentage, 0) }}%</div>
                            <div class="text-xs text-gray-500">{{ $deviceLabels[$device] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Browsers --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Navigateurs</h3>
                <div class="space-y-2">
                    @php $totalBrowsers = array_sum(array_column($browserStats, 'visits')); @endphp
                    @forelse($browserStats as $browser)
                        @php $percentage = $totalBrowsers > 0 ? ($browser->visits / $totalBrowsers) * 100 : 0; @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $browser->browser ?? 'Autre' }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-900 dark:text-white w-10 text-right">{{ number_format($percentage, 0) }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center">Pas de données</p>
                    @endforelse
                </div>
            </div>

            {{-- OS --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Systèmes d'exploitation</h3>
                <div class="space-y-2">
                    @php $totalOS = array_sum(array_column($osStats, 'visits')); @endphp
                    @forelse($osStats as $os)
                        @php $percentage = $totalOS > 0 ? ($os->visits / $totalOS) * 100 : 0; @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $os->os ?? 'Autre' }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-900 dark:text-white w-10 text-right">{{ number_format($percentage, 0) }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center">Pas de données</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pages Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Pages --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pages les plus visitées</h3>
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
                        'auth' => 'Connexion/Inscription',
                        'client_booking' => 'Ma réservation',
                        'other' => 'Autres',
                    ];
                    $totalPageVisits = array_sum(array_column($topPages, 'visits'));
                @endphp
                <div class="space-y-3">
                    @forelse($topPages as $page)
                        @php $percentage = $totalPageVisits > 0 ? ($page->visits / $totalPageVisits) * 100 : 0; @endphp
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

            {{-- Landing Pages --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pages d'entrée (Landing)</h3>
                @php $totalLanding = array_sum(array_column($landingPages, 'visits')); @endphp
                <div class="space-y-3">
                    @forelse($landingPages as $page)
                        @php $percentage = $totalLanding > 0 ? ($page->visits / $totalLanding) * 100 : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">{{ $pageLabels[$page->page_type] ?? $page->page_type }}</span>
                                <span class="text-gray-500">{{ number_format($page->visits) }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Pas encore de données</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- IP Details Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                Visiteurs par IP (ce mois)
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                Total : {{ number_format($totalRecords) }} visites enregistrées
            </p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">IP</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Visites</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Première visite</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Dernière visite</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($topIps as $ipData)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-2 px-3">
                                    <code class="bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded text-xs">{{ $ipData->ip_address }}</code>
                                </td>
                                <td class="py-2 px-3 font-medium text-gray-900 dark:text-white">{{ number_format($ipData->visits) }}</td>
                                <td class="py-2 px-3 text-gray-500">{{ \Carbon\Carbon::parse($ipData->first_visit)->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-3 text-gray-500">{{ \Carbon\Carbon::parse($ipData->last_visit)->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-3">
                                    @if($ipData->ip_address === $currentIp)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            C'est vous
                                        </span>
                                    @elseif(in_array($ipData->ip_address, $excludedIps))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Exclu
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">Pas encore de données</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Visits --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dernières visites (50)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Date</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Localisation</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Page</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Appareil</th>
                            <th class="text-left py-2 px-3 text-gray-500 font-medium">Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentVisits as $visit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $visit->ip_address === $currentIp ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                <td class="py-2 px-3 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($visit->visited_at)->format('d/m H:i') }}</td>
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-2">
                                        @if($visit->country_code)
                                            <img src="https://flagcdn.com/16x12/{{ strtolower($visit->country_code) }}.png" alt="" class="w-4 h-3 rounded">
                                        @endif
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $visit->city ?? $visit->country ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-gray-700 dark:text-gray-300">
                                    {{ $pageLabels[$visit->page_type] ?? $visit->page_type }}
                                    @if($visit->is_landing)
                                        <span class="ml-1 text-xs text-green-600">Landing</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-gray-500">
                                    {{ ucfirst($visit->device_type ?? '-') }} / {{ $visit->browser ?? '-' }}
                                </td>
                                <td class="py-2 px-3 text-gray-500">
                                    {{ $visit->traffic_source ?? 'Direct' }}
                                    @if($visit->utm_campaign)
                                        <span class="ml-1 text-xs text-purple-600">({{ $visit->utm_campaign }})</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">Pas encore de visites</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Vehicles --}}
        @if(count($topVehicles) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Véhicules les plus consultés</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach($topVehicles as $item)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->full_name }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $item->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($item->visits) }} vues</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-filament-panels::page>
