@extends('front.layouts.app')

@section('title', \App\Models\Setting::get('company_name', 'ResaDZ') . ' - ' . \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie'))
@section('meta_description', 'Marketplace de location de voitures en Algérie. Comparez et réservez auprès de loueurs vérifiés partout en Algérie.')

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "{{ \App\Models\Setting::get('company_name', 'ResaDZ') }}",
    "url": "{{ config('app.url') }}",
    "logo": "{{ \App\Models\Setting::get('logo_light') ? asset('storage/' . \App\Models\Setting::get('logo_light')) : asset('assets/favicon.png') }}",
    "description": "Marketplace de location de voitures en Algérie. Comparez et réservez auprès de loueurs vérifiés.",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "DZ"
    },
    "sameAs": [
        @if(\App\Models\Setting::get('facebook'))"{{ \App\Models\Setting::get('facebook') }}"@endif
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "{{ \App\Models\Setting::get('company_name', 'ResaDZ') }}",
    "url": "{{ config('app.url') }}",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ route('vehicles.index') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endsection

@section('content')

    <!-- Hero Section with Slider -->
    <section class="relative min-h-[600px] lg:min-h-[700px] flex items-center">
        {{-- Background Slider --}}
        <div class="absolute inset-0 z-0">
            @if(isset($heroSlides) && $heroSlides->count() > 0)
                <div id="hero-slider" class="relative w-full h-full">
                    @foreach($heroSlides as $index => $slide)
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-index="{{ $index }}">
                            <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title ?? 'ResaDZ' }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
                @if($heroSlides->count() > 1)
                    <div class="absolute bottom-24 left-1/2 transform -translate-x-1/2 z-20 flex gap-2">
                        @foreach($heroSlides as $index => $slide)
                            <button onclick="goToSlide({{ $index }})" aria-label="Slide {{ $index + 1 }}" class="hero-dot w-3 h-3 rounded-full transition-all {{ $index === 0 ? 'bg-white scale-110' : 'bg-white/50 hover:bg-white/70' }}" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                @endif
            @elseif(file_exists(public_path('assets/hero.jpg')))
                <img src="{{ asset('assets/hero.jpg') }}" alt="Location de voitures en Algérie" class="w-full h-full object-cover">
            @elseif(file_exists(public_path('assets/hero.png')))
                <img src="{{ asset('assets/hero.png') }}" alt="Location de voitures en Algérie" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-900 via-gray-800 to-black"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-neutral-950"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="max-w-3xl">
                @if(isset($heroSlides) && $heroSlides->count() > 0)
                    <div class="relative">
                        @foreach($heroSlides as $index => $slide)
                            <div class="hero-title transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0 absolute top-0 left-0' }}" data-index="{{ $index }}">
                                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                                    @if($slide->title)
                                        {{ $slide->title }}
                                        @if($slide->subtitle)
                                            <span class="block text-green-400">{{ $slide->subtitle }}</span>
                                        @endif
                                    @else
                                        Louez votre voiture
                                        <span class="block text-green-400">partout en Algérie</span>
                                    @endif
                                </h1>
                                @if($slide->button_text && $slide->button_url)
                                    <a href="{{ $slide->button_url }}" class="mt-6 inline-flex items-center px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition shadow-lg shadow-green-600/30">
                                        {{ $slide->button_text }}
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                        {{ $homeContent['hero_title'] ?? 'Louez votre voiture' }}
                        <span class="block text-green-400">{{ $homeContent['hero_subtitle'] ?? 'partout en Algérie' }}</span>
                    </h1>
                @endif
                <p class="mt-6 text-lg sm:text-xl text-white/70 max-w-xl">
                    {{ $homeContent['hero_description'] ?? 'Comparez les offres de loueurs vérifiés et réservez en quelques clics. Le meilleur de la location auto en DZ.' }}
                </p>

                <!-- Search Form Card -->
                <div class="mt-10 bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl p-6 lg:p-8 border border-white/10">
                    <form action="{{ route('vehicles.index') }}" method="GET" id="search-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="hero-wilaya" class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Lieu de prise en charge</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 text-white/40 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <select name="wilaya" id="hero-wilaya" class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none cursor-pointer">
                                        <option value="">Wilaya, ville...</option>
                                        @if(isset($wilayas))
                                            @foreach($wilayas as $wilaya)
                                                <option value="{{ $wilaya }}">{{ $wilaya }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="hero-pickup-date" class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Date de départ</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 text-white/40 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <input type="date" name="pickup_date" id="hero-pickup-date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 cursor-pointer">
                                </div>
                            </div>
                            <div>
                                <label for="hero-return-date" class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Date de retour</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 text-white/40 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <input type="date" name="return_date" id="hero-return-date" value="{{ date('Y-m-d', strtotime('+4 days')) }}" class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 cursor-pointer">
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="w-full px-6 py-3.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-500 transition shadow-lg shadow-green-600/30 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Stats -->
                <div class="mt-10 flex flex-wrap gap-8 lg:gap-12">
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ $totalVehicles }}+</div>
                        <div class="text-sm text-white/50 mt-1">Véhicules</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ $totalLoueurs }}+</div>
                        <div class="text-sm text-white/50 mt-1">Loueurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ $wilayas->count() }}+</div>
                        <div class="text-sm text-white/50 mt-1">Wilayas</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notre sélection pour vous -->
    @if($selectedVehicles->count() > 0)
    <section class="relative py-16 lg:py-24 bg-gradient-to-b from-neutral-950 via-neutral-900 to-neutral-950">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8 lg:mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-amber-400 to-amber-600 rounded-full"></div>
                        <span class="text-amber-400 text-sm font-semibold uppercase tracking-wider">Recommandé</span>
                    </div>
                    <h2 class="text-2xl lg:text-4xl font-black text-white tracking-tight">{{ $homeContent['selection_title'] ?? 'Notre sélection' }}</h2>
                    <p class="mt-2 text-white/50 text-sm lg:text-base">{{ $homeContent['selection_subtitle'] ?? 'Les véhicules que nous recommandons' }}</p>
                </div>
                @if($selectedVehicles->count() > 4)
                <a href="{{ route('vehicles.index', ['selection' => 1]) }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                @endif
            </div>

            <div class="hidden lg:grid grid-cols-4 gap-6">
                @foreach($selectedVehicles->take(4) as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle, 'showSelectionBorder' => true])
                @endforeach
            </div>
            <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-4 px-4">
                <div class="flex gap-4" style="width: max-content;">
                    @foreach($selectedVehicles->take(4) as $vehicle)
                        <div class="w-[280px] flex-shrink-0">
                            @include('front.components.vehicle-card', ['vehicle' => $vehicle, 'showSelectionBorder' => true])
                        </div>
                    @endforeach
                </div>
            </div>
            @if($selectedVehicles->count() > 4)
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('vehicles.index', ['selection' => 1]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>
    @endif

    <!-- Citadines -->
    @if(isset($vehiclesByCategory['citadine']) && $vehiclesByCategory['citadine']->count() > 0)
    <section class="relative py-16 lg:py-24 bg-neutral-950">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8 lg:mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full"></div>
                        <span class="text-blue-400 text-sm font-semibold uppercase tracking-wider">Urbain</span>
                    </div>
                    <h2 class="text-2xl lg:text-4xl font-black text-white tracking-tight">Citadines</h2>
                    <p class="mt-2 text-white/50 text-sm lg:text-base">Économiques et pratiques pour la ville</p>
                </div>
                <a href="{{ route('vehicles.index', ['category' => 'citadine']) }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="hidden lg:grid grid-cols-4 gap-6">
                @foreach($vehiclesByCategory['citadine'] as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
            <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-4 px-4">
                <div class="flex gap-4" style="width: max-content;">
                    @foreach($vehiclesByCategory['citadine'] as $vehicle)
                        <div class="w-[280px] flex-shrink-0">
                            @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('vehicles.index', ['category' => 'citadine']) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>
    @endif

    <!-- Transfer/Taxi Section -->
    @include('front.components.transfer-section')

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>

    <!-- Berlines -->
    @if(isset($vehiclesByCategory['berline']) && $vehiclesByCategory['berline']->count() > 0)
    <section class="relative py-16 lg:py-24 bg-gradient-to-b from-neutral-950 via-neutral-900 to-neutral-950">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8 lg:mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></div>
                        <span class="text-purple-400 text-sm font-semibold uppercase tracking-wider">Premium</span>
                    </div>
                    <h2 class="text-2xl lg:text-4xl font-black text-white tracking-tight">Berlines</h2>
                    <p class="mt-2 text-white/50 text-sm lg:text-base">Confort et élégance pour vos trajets</p>
                </div>
                <a href="{{ route('vehicles.index', ['category' => 'berline']) }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="hidden lg:grid grid-cols-4 gap-6">
                @foreach($vehiclesByCategory['berline'] as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
            <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-4 px-4">
                <div class="flex gap-4" style="width: max-content;">
                    @foreach($vehiclesByCategory['berline'] as $vehicle)
                        <div class="w-[280px] flex-shrink-0">
                            @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('vehicles.index', ['category' => 'berline']) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>
    @endif

    <!-- SUV -->
    @if(isset($vehiclesByCategory['suv']) && $vehiclesByCategory['suv']->count() > 0)
    <section class="relative py-16 lg:py-24 bg-neutral-950">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-1/3 w-96 h-96 bg-green-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8 lg:mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></div>
                        <span class="text-green-400 text-sm font-semibold uppercase tracking-wider">Aventure</span>
                    </div>
                    <h2 class="text-2xl lg:text-4xl font-black text-white tracking-tight">SUV</h2>
                    <p class="mt-2 text-white/50 text-sm lg:text-base">Puissance et polyvalence pour tous vos trajets</p>
                </div>
                <a href="{{ route('vehicles.index', ['category' => 'suv']) }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/15 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="hidden lg:grid grid-cols-4 gap-6">
                @foreach($vehiclesByCategory['suv'] as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
            <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-4 px-4">
                <div class="flex gap-4" style="width: max-content;">
                    @foreach($vehiclesByCategory['suv'] as $vehicle)
                        <div class="w-[280px] flex-shrink-0">
                            @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('vehicles.index', ['category' => 'suv']) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold rounded-full transition border border-white/10">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>
    @endif

    <!-- Airport Search Section -->
    @include('front.components.airport-search')

    <!-- Blog Section - Actualités -->
    @if(isset($blogPosts) && $blogPosts->count() > 0)
    <section class="relative py-24 bg-gradient-to-b from-neutral-900 via-neutral-950 to-black overflow-hidden">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></div>
                        <span class="text-green-400 text-sm font-semibold uppercase tracking-wider">ResaDZ Magazine</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white">Actualités</h2>
                    <p class="mt-2 text-white/50">Promotions, guides et conseils pour votre location</p>
                </div>
                <a href="{{ route('blog.index') }}" class="hidden sm:inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-full transition backdrop-blur-sm border border-white/10">
                    Tout voir
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($blogPosts as $post)
                    @include('front.components.blog-card', ['post' => $post])
                @endforeach
            </div>

            <div class="mt-10 text-center sm:hidden">
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-green-600 hover:bg-green-500 text-white text-sm font-bold rounded-full transition">
                    Voir toutes les actualités
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>
    @endif

    <!-- Loueurs Section -->
    @if($loueurs->count() > 0)
    <section class="relative py-20 bg-gradient-to-b from-neutral-950 to-neutral-900 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/2 right-0 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="w-8 h-1 bg-gradient-to-r from-amber-400 to-amber-600 rounded-full"></div>
                    <span class="text-amber-400 text-sm font-semibold uppercase tracking-wider">Réseau</span>
                    <div class="w-8 h-1 bg-gradient-to-r from-amber-400 to-amber-600 rounded-full"></div>
                </div>
                <h2 class="text-3xl font-bold text-white">{{ $homeContent['loueurs_title'] ?? 'Nos loueurs partenaires' }}</h2>
                <p class="mt-2 text-white/50">{{ $homeContent['loueurs_subtitle'] ?? 'Des professionnels vérifiés à votre service' }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($loueurs as $loueur)
                    <a href="{{ route('loueur.show', $loueur->slug) }}" class="group bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 hover:border-white/20 transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-green-500/5">
                        <div class="flex items-center gap-4">
                            @if($loueur->logo)
                                <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                            @else
                                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex flex-col items-center justify-center shadow-sm">
                                    <span class="text-black font-black text-[10px] leading-none">PARTENAIRE</span>
                                    <span class="text-black font-black text-xs leading-tight">ResaDZ</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-white group-hover:text-green-400 transition truncate">{{ $loueur->company_name }}</h3>
                                <p class="text-sm text-white/50">{{ $loueur->city ?? $loueur->wilaya ?? 'Algérie' }}</p>
                                @if($loueur->total_reviews > 0)
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= round($loueur->rating) ? 'text-amber-400' : 'text-white/20' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm font-semibold text-white">{{ number_format($loueur->rating, 1) }}</span>
                                        <span class="text-xs text-white/40">({{ $loueur->total_reviews }})</span>
                                    </div>
                                @else
                                    <p class="text-xs text-white/40 mt-1">Nouveau partenaire</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm text-white/50">{{ $loueur->vehicles_count }} véhicule{{ $loueur->vehicles_count > 1 ? 's' : '' }}</span>
                            <div class="flex items-center gap-2">
                                @if($loueur->rating >= 4.5 && $loueur->total_reviews >= 10)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-400 bg-amber-500/10 px-2 py-1 rounded-full border border-amber-500/30">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        Top
                                    </span>
                                @endif
                                @if($loueur->is_verified)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-400 bg-green-500/10 px-2 py-1 rounded-full border border-green-500/30">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Vérifié
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Separator --}}
    <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>
    @endif

    <!-- CTA -->
    <section class="relative py-20 bg-gradient-to-r from-green-900 via-green-800 to-green-900 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-black/20 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white">{{ $homeContent['cta_title'] ?? 'Vous êtes loueur de voitures ?' }}</h2>
            <p class="mt-4 text-white/70 text-lg">{{ $homeContent['cta_description'] ?? 'Rejoignez ResaDZ et développez votre activité en ligne. Gérez vos véhicules, réservations et finances depuis un seul tableau de bord.' }}</p>
            <a href="/loueur" class="mt-8 inline-flex items-center px-8 py-4 bg-white text-green-800 font-bold rounded-full hover:bg-green-50 transition shadow-xl">
                {{ $homeContent['cta_button'] ?? 'Devenir partenaire' }}
            </a>
        </div>
    </section>

@endsection

@section('scripts')
<script>
    // Hero Slider
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.hero-slide');
        const titles = document.querySelectorAll('.hero-title');
        const dots = document.querySelectorAll('.hero-dot');

        if (slides.length <= 1) return;

        let currentSlide = 0;
        const totalSlides = slides.length;
        let autoSlideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('opacity-100', i === index);
                slide.classList.toggle('opacity-0', i !== index);
            });
            titles.forEach((title, i) => {
                if (i === index) {
                    title.classList.remove('opacity-0', 'absolute', 'top-0', 'left-0');
                    title.classList.add('opacity-100');
                } else {
                    title.classList.remove('opacity-100');
                    title.classList.add('opacity-0', 'absolute', 'top-0', 'left-0');
                }
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === index);
                dot.classList.toggle('scale-110', i === index);
                dot.classList.toggle('bg-white/50', i !== index);
            });
            currentSlide = index;
        }

        function nextSlide() {
            showSlide((currentSlide + 1) % totalSlides);
        }

        window.goToSlide = function(index) {
            showSlide(index);
            resetAutoSlide();
        };

        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(nextSlide, 5000);
        }

        autoSlideInterval = setInterval(nextSlide, 5000);
    });
</script>
@endsection
