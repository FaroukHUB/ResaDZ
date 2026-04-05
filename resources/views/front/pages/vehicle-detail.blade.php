@extends('front.layouts.app')

@section('title', $vehicle->full_name . ' - Location ResaDZ')
@section('meta_description', 'Louez ' . $vehicle->full_name . ' à partir de ' . number_format($vehicle->price_per_day, 0, ',', ' ') . ' DA/jour chez ' . ($vehicle->loueur->company_name ?? 'ResaDZ'))
@section('og_title', $vehicle->full_name . ' - ' . number_format($vehicle->price_per_day, 0, ',', ' ') . ' DA/jour')
@section('og_image', $vehicle->image ? asset('storage/' . $vehicle->image) : '')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<style>
    #vehicle-availability-calendar .fc-daygrid-day.fc-day-today { background: #fffbeb !important; }
    #vehicle-availability-calendar .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 700; }
    #vehicle-availability-calendar .fc-button-primary { background: #111827 !important; border-color: #111827 !important; }
    #vehicle-availability-calendar .fc-button-primary:hover { background: #374151 !important; }
    #vehicle-availability-calendar .unavailable-bg { opacity: 0.6; }
    #vehicle-availability-calendar .fc-daygrid-day-number { font-weight: 600; font-size: 0.85rem; }
</style>
@endsection

@section('meta_extra')
<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Car",
    "name": "{{ $vehicle->full_name }}",
    "brand": {
        "@@type": "Brand",
        "name": "{{ $vehicle->brand->name ?? '' }}"
    },
    "fuelType": "{{ $vehicle->fuel_type }}",
    "numberOfDoors": {{ $vehicle->doors ?? 5 }},
    "vehicleTransmission": "{{ $vehicle->transmission }}",
    "seatingCapacity": {{ $vehicle->seats ?? 5 }},
    "offers": {
        "@@type": "Offer",
        "price": "{{ $vehicle->price_per_day }}",
        "priceCurrency": "DZD",
        "availability": "https://schema.org/InStock"
    }
}
</script>
@endsection

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Accueil</a>
            <span>/</span>
            <a href="{{ route('vehicles.index') }}" class="hover:text-amber-600">Véhicules</a>
            <span>/</span>
            <span class="text-gray-900">{{ $vehicle->full_name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Images + Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Image -->
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 relative">
                    <div class="aspect-[16/10] bg-gray-100">
                        @if($vehicle->display_image)
                            <img src="{{ asset('storage/' . $vehicle->display_image) }}" alt="{{ $vehicle->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            </div>
                        @endif
                    </div>
                    @if($vehicle->loueur && $vehicle->loueur->company_name)
                        <div class="absolute bottom-4 right-4" style="perspective: 250px;">
                            <span class="block bg-black/60 backdrop-blur-sm px-4 py-1.5 rounded text-white text-sm font-bold tracking-wide uppercase"
                                  style="transform: rotateY(-8deg) rotateX(3deg); text-shadow: 0 1px 4px rgba(0,0,0,0.5);">
                                {{ $vehicle->loueur->company_name }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Gallery -->
                @if($vehicle->gallery && count($vehicle->gallery) > 0)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($vehicle->gallery as $photo)
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $vehicle->full_name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer">
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Specs -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Caractéristiques</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Transmission</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->transmission === 'automatic' ? 'Automatique' : 'Manuelle' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Carburant</div>
                            <div class="font-bold text-gray-900 mt-1">{{ ucfirst($vehicle->fuel_type) }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Places</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->seats }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Portes</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->doors ?? 5 }}</div>
                        </div>
                        @if($vehicle->year)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Année</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->year }}</div>
                        </div>
                        @endif
                        @if($vehicle->color)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Couleur</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->color }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Équipements -->
                @php $activeFeatures = $vehicle->getActiveFeatures(); @endphp
                @if(count($activeFeatures) > 0)
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Équipements</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($activeFeatures as $feature)
                        <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                            <span class="text-lg">{{ $feature['icon'] }}</span>
                            <span class="text-sm font-semibold text-green-800">{{ $feature['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Calendrier de disponibilité -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Disponibilité</h2>
                    <div class="flex items-center gap-4 mb-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Disponible
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-300 inline-block"></span> Indisponible
                        </span>
                    </div>
                    <div id="vehicle-availability-calendar" data-vehicle-slug="{{ $vehicle->slug }}"></div>
                </div>

                <!-- Loueur Info -->
                @if($vehicle->loueur)
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Loueur</h2>
                    <a href="{{ route('loueur.show', $vehicle->loueur->slug) }}" class="flex items-center gap-4 group">
                        @if($vehicle->loueur->logo)
                            <img src="{{ asset('storage/' . $vehicle->loueur->logo) }}" alt="{{ $vehicle->loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                        @else
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($vehicle->loueur->company_name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-gray-900 group-hover:text-amber-600 transition">{{ $vehicle->loueur->company_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $vehicle->loueur->city ?? '' }} {{ $vehicle->loueur->wilaya ? '- ' . $vehicle->loueur->wilaya : '' }}</p>
                            @if($vehicle->loueur->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Loueur vérifié
                                </span>
                            @endif
                        </div>
                    </a>
                </div>
                @endif
            </div>

            <!-- Right: Booking Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24 space-y-5">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->full_name }}</h1>
                        @if($vehicle->brand)
                            <p class="text-gray-500">{{ $vehicle->brand->name }} {{ $vehicle->category ? '- ' . $vehicle->category->name : '' }}</p>
                        @endif
                    </div>

                    <!-- Price -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5">
                        <div class="text-sm text-amber-700 font-medium">Prix par jour</div>
                        <div class="mt-1">
                            <span class="text-4xl font-black text-amber-600">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }}</span>
                            <span class="text-lg text-amber-700 ml-1">DA</span>
                        </div>
                        @if($vehicle->price_per_day_eur)
                            <div class="text-sm text-amber-600 mt-1">~ {{ number_format($vehicle->price_per_day_eur, 0) }} EUR</div>
                        @endif
                    </div>

                    {{-- Haute saison badge --}}
                    @php
                        $now = now();
                        $activeSeasonNow = $vehicle->seasonalRates
                            ->filter(fn ($rate) => $rate->is_active && $rate->start_date->lte($now) && $rate->end_date->gte($now))
                            ->first();
                    @endphp
                    @if($activeSeasonNow)
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    🌞 Haute saison
                                </span>
                                <span class="text-sm font-semibold text-amber-800">{{ $activeSeasonNow->name }}</span>
                            </div>
                            <p class="text-sm text-amber-700 mt-2">
                                + {{ number_format($activeSeasonNow->supplement_amount, 0, ',', ' ') }} DA/jour
                                <span class="text-amber-500">(du {{ $activeSeasonNow->start_date->format('d/m') }} au {{ $activeSeasonNow->end_date->format('d/m') }})</span>
                            </p>
                        </div>
                    @endif

                    {{-- Livraison aéroport offerte --}}
                    @php
                        $freeAirportDays = $vehicle->loueur ? (int) $vehicle->loueur->getSetting('free_airport_delivery_days', 0) : 0;
                    @endphp
                    @if($freeAirportDays > 0)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    ✈️ Livraison aéroport offerte
                                </span>
                            </div>
                            <p class="text-sm text-green-700 mt-2">
                                Livraison aéroport gratuite à partir de <strong>{{ $freeAirportDays }} jours</strong> de location
                            </p>
                        </div>
                    @endif

                    {{-- Special Offer Banner --}}
                    @if($vehicle->activeOffer)
                        @php
                            $offer = $vehicle->activeOffer;
                            $discountText = $offer->discount_type === 'percentage'
                                ? '-' . number_format($offer->discount_value, 0) . '%'
                                : '-' . number_format($offer->discount_value, 0, ',', ' ') . ' DA';
                        @endphp
                        <div class="bg-gradient-to-r from-red-600 to-orange-500 rounded-xl p-4 text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                    <span class="text-lg font-black">{{ $offer->badge_text }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-lg">{{ $offer->title }}</div>
                                    <div class="text-sm text-white/90">{{ $discountText }} sur votre location</div>
                                </div>
                            </div>
                            @if($offer->description)
                                <p class="text-sm text-white/80 mt-3 border-t border-white/20 pt-3">{{ $offer->description }}</p>
                            @endif
                            <div class="text-xs text-white/70 mt-2">
                                Valable jusqu'au {{ $offer->end_date->format('d/m/Y') }}
                            </div>
                        </div>
                    @endif

                    <!-- Réserver -->
                    @if($vehicle->status === 'reserved')
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                            <div class="flex items-center justify-center gap-2 text-blue-700 font-bold mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Indisponible
                            </div>
                            <p class="text-sm text-blue-600">Ce véhicule est actuellement indisponible. Revenez bientôt !</p>
                        </div>
                    @else
                        <a href="{{ route('booking.create', $vehicle->slug) }}"
                           data-track="reserve"
                           data-vehicle-id="{{ $vehicle->id }}"
                           data-loueur-id="{{ $vehicle->loueur?->id }}"
                           class="flex items-center justify-center gap-2 w-full py-4 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Réserver ce véhicule
                        </a>
                    @endif


                    <!-- Payment Methods -->
                    @if($vehicle->loueur && $vehicle->loueur->payment_methods)
                        <div>
                            <div class="text-sm font-medium text-gray-700 mb-2">Paiements acceptés</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vehicle->loueur->payment_methods as $method)
                                    @php
                                        $methodLabels = [
                                            'cash' => 'Espèces',
                                            'cib' => 'CIB',
                                            'dahabia' => 'Dahabia',
                                            'baridimob' => 'BaridiMob',
                                            'paypal' => 'PayPal',
                                            'bank_transfer' => 'Virement',
                                            'wise' => 'En ligne (Wise, Revolut...)',
                                        ];
                                    @endphp
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                                        {{ $methodLabels[$method] ?? $method }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Reviews section --}}
        @if($vehicle->loueur)
            @include('front.components.reviews-section', ['loueur' => $vehicle->loueur])
        @endif

        {{-- Related Vehicles --}}
        @if($relatedVehicles->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Véhicules similaires</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedVehicles as $relVehicle)
                        <a href="{{ route('vehicles.show', $relVehicle->slug) }}" class="block bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition">
                            <div class="aspect-[16/10] bg-gray-100">
                                @if($relVehicle->image)
                                    <img src="{{ asset('storage/' . $relVehicle->image) }}" alt="{{ $relVehicle->full_name }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">{{ $relVehicle->full_name }}</h3>
                                <p class="text-amber-600 font-bold mt-1">{{ number_format($relVehicle->price_per_day, 0, ',', ' ') }} DA/jour</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

@if(\App\Models\Setting::get('facebook_pixel_id'))
<script>
fbq('track','ViewContent',{content_name:'{{ addslashes($vehicle->full_name) }}',content_category:'{{ $vehicle->category->name ?? "" }}',value:{{ $vehicle->price_per_day }},currency:'DZD'});
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/fr.global.min.js"></script>
<script src="{{ asset('js/vehicle-calendar.js') }}"></script>

@endsection
