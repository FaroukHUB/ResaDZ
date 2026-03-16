@extends('front.layouts.app')

@section('title', \App\Models\Setting::get('company_name', 'ResaDZ') . ' - ' . \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie'))
@section('meta_description', 'Marketplace de location de voitures en Algérie. Comparez et réservez auprès de loueurs vérifiés partout en Algérie.')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endsection

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
    <section class="relative min-h-[70vh] sm:min-h-[600px] lg:min-h-[700px] flex items-end sm:items-center pb-24 sm:pb-32 lg:pb-40">
        {{-- Background Slider --}}
        <div class="absolute inset-0 z-0">
            @if(isset($heroSlides) && $heroSlides->count() > 0)
                <div id="hero-slider" class="relative w-full h-full">
                    @foreach($heroSlides as $index => $slide)
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-index="{{ $index }}">
                            <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title ?? 'ResaDZ' }}" class="w-full h-full object-cover object-center">
                        </div>
                    @endforeach
                </div>
                @if($heroSlides->count() > 1)
                    <div class="absolute bottom-4 sm:bottom-24 left-1/2 transform -translate-x-1/2 z-20 flex gap-2">
                        @foreach($heroSlides as $index => $slide)
                            <button onclick="goToSlide({{ $index }})" class="hero-dot w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-all {{ $index === 0 ? 'bg-white scale-110' : 'bg-white/50' }}" data-index="{{ $index }}"></button>
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
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
        </div>

        <div class="relative z-10 w-full px-4 sm:px-6 lg:px-8 sm:max-w-7xl sm:mx-auto">
            <div class="max-w-3xl">
                @if(isset($heroSlides) && $heroSlides->count() > 0)
                    <div class="relative">
                        @foreach($heroSlides as $index => $slide)
                            <div class="hero-title transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0 absolute top-0 left-0' }}" data-index="{{ $index }}">
                                <h1 class="text-xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                                    @if($slide->title)
                                        {{ $slide->title }}
                                        @if($slide->subtitle)
                                            <span class="block text-green-400 text-lg sm:text-4xl lg:text-5xl">{{ $slide->subtitle }}</span>
                                        @endif
                                    @else
                                        Louez votre voiture
                                        <span class="block text-green-400">partout en Algérie</span>
                                    @endif
                                </h1>
                                @if($slide->button_text && $slide->button_url)
                                    <a href="{{ $slide->button_url }}" class="hidden sm:inline-flex mt-6 items-center px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition">
                                        {{ $slide->button_text }}
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <h1 class="text-xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                        {{ $homeContent['hero_title'] ?? 'Louez votre voiture' }}
                        <span class="block text-green-400">{{ $homeContent['hero_subtitle'] ?? 'partout en Algérie' }}</span>
                    </h1>
                @endif
                <div class="mt-3 sm:mt-10 flex gap-4 sm:gap-12">
                    <div class="text-center">
                        <div class="text-lg sm:text-4xl font-bold text-white">{{ $totalVehicles }}+</div>
                        <div class="text-xs sm:text-sm text-white/50">Véhicules</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg sm:text-4xl font-bold text-white">{{ $totalLoueurs }}+</div>
                        <div class="text-xs sm:text-sm text-white/50">Loueurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg sm:text-4xl font-bold text-white">{{ $wilayas->count() }}+</div>
                        <div class="text-xs sm:text-sm text-white/50">Wilayas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form Card - Desktop only -->
        <div class="absolute bottom-0 left-0 right-0 z-20 transform translate-y-1/2" style="display:none" id="desktop-search-form">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-neutral-900 rounded-2xl shadow-2xl p-6 lg:p-8 border border-neutral-800 overflow-hidden">
                    <form action="{{ route('vehicles.index') }}" method="GET" id="search-form">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            <div class="min-w-0">
                                <label for="hero-wilaya" class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Lieu de prise en charge</label>
                                <select name="wilaya" id="hero-wilaya" class="w-full px-4 py-3 rounded-xl bg-neutral-800 border border-neutral-700 text-white appearance-none cursor-pointer text-sm">
                                    <option value="">Wilaya, ville...</option>
                                    @if(isset($wilayas))
                                        @foreach($wilayas as $wilaya)
                                            <option value="{{ $wilaya }}">{{ $wilaya }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="min-w-0">
                                <label for="hero-pickup-date" class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Date de départ</label>
                                <input type="date" name="pickup_date" id="hero-pickup-date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-4 py-3 rounded-xl bg-neutral-800 border border-neutral-700 text-white cursor-pointer text-sm" style="color-scheme:dark">
                            </div>
                            <div class="min-w-0">
                                <label for="hero-return-date" class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Date de retour</label>
                                <input type="date" name="return_date" id="hero-return-date" value="{{ date('Y-m-d', strtotime('+4 days')) }}" class="w-full px-4 py-3 rounded-xl bg-neutral-800 border border-neutral-700 text-white cursor-pointer text-sm" style="color-scheme:dark">
                            </div>
                            <div>
                                <button type="submit" class="w-full px-6 py-3.5 bg-green-600 text-white font-bold rounded-xl flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>if(window.innerWidth>=640)document.getElementById('desktop-search-form').style.display='block';</script>
    </section>

    <!-- Search Form Mobile - Chevauche le hero -->
    <div class="bg-neutral-900 mx-4 rounded-2xl p-4 relative z-30 sm:hidden" style="margin-top:-20px" id="mobile-search-form">
        <form action="{{ route('vehicles.index') }}" method="GET">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Lieu de prise en charge</label>
                    <select name="wilaya" class="w-full px-4 py-3 rounded-xl bg-neutral-800 border border-neutral-700 text-white appearance-none cursor-pointer text-sm">
                        <option value="">Wilaya, ville...</option>
                        @if(isset($wilayas))
                            @foreach($wilayas as $wilaya)
                                <option value="{{ $wilaya }}">{{ $wilaya }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Date de départ</label>
                    <input type="date" name="pickup_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-4 py-3 rounded-xl bg-neutral-800 border border-neutral-700 text-white cursor-pointer text-sm" style="color-scheme: dark;">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-white/70 uppercase tracking-wide mb-2">Date de retour</label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d', strtotime('+4 days')) }}" class="w-full px-4 py-3 rounded-xl bg-neutral-800 border border-neutral-700 text-white cursor-pointer text-sm" style="color-scheme: dark;">
                </div>
                <button type="submit" class="w-full px-6 py-3.5 bg-green-600 text-white font-bold rounded-xl flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Rechercher
                </button>
            </div>
        </form>
    </div>

    <!-- Notre sélection pour vous -->
    @if($selectedVehicles->count() > 0)
    <section class="pb-16 lg:pb-24 bg-gray-100 pt-8 sm:pt-[180px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8 lg:mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-700 rounded-full"></div>
                        <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Recommandé</span>
                    </div>
                    <h2 class="text-2xl lg:text-4xl font-black text-gray-900 tracking-tight">{{ $homeContent['selection_title'] ?? 'Notre sélection pour vous' }}</h2>
                    <p class="mt-2 text-gray-500 text-sm lg:text-base">{{ $homeContent['selection_subtitle'] ?? 'Les véhicules que nous recommandons' }}</p>
                </div>
                @if($selectedVehicles->count() > 8)
                <a href="{{ route('vehicles.index', ['selection' => 1]) }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full transition">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                @endif
            </div>

            {{-- Desktop: grid 4 colonnes, max 8 véhicules (2 rows) --}}
            <div class="hidden lg:grid grid-cols-4 gap-6">
                @foreach($selectedVehicles->take(8) as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle, 'showSelectionBorder' => true])
                @endforeach
            </div>
            {{-- Mobile: scroll horizontal --}}
            <div class="lg:hidden overflow-x-auto scrollbar-hide -mx-4 px-4">
                <div class="flex gap-4" style="width: max-content;">
                    @foreach($selectedVehicles->take(8) as $vehicle)
                        <div class="w-[280px] flex-shrink-0">
                            @include('front.components.vehicle-card', ['vehicle' => $vehicle, 'showSelectionBorder' => true])
                        </div>
                    @endforeach
                </div>
            </div>
            @if($selectedVehicles->count() > 8)
            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('vehicles.index', ['selection' => 1]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full transition">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
            @endif

            {{-- Bouton voir tous les véhicules --}}
            <div class="mt-10 text-center">
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full transition shadow-lg shadow-green-600/20 text-lg">
                    Voir tous nos véhicules
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Qui sommes-nous -->
    <section class="py-16 lg:py-24 bg-neutral-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="w-8 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full"></div>
                    <span class="text-green-400 text-sm font-semibold uppercase tracking-wider">ResaDZ</span>
                    <div class="w-8 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full"></div>
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-white">Qui sommes-nous ?</h2>
                <p class="mt-3 text-white/50 max-w-2xl mx-auto">La première marketplace algérienne dédiée à la location de véhicules et aux transferts</p>
                <div class="mt-4 inline-flex items-center gap-2 bg-green-500/10 border border-green-500/20 rounded-full px-4 py-1.5">
                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span class="text-green-400 text-sm font-medium">Inscription gratuite &mdash; Tarifs transparents</span>
                    <span class="text-white/30">|</span>
                    <a href="{{ route('comment-ca-marche') }}" class="text-white/50 hover:text-green-400 text-sm transition">Comment ça marche</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Card 1 --}}
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition group">
                    <div class="w-14 h-14 bg-green-500/20 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-green-500/30 transition">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Comparez les offres</h3>
                    <p class="text-white/50 text-sm leading-relaxed">Accédez à des dizaines de loueurs vérifiés et comparez les prix, options et disponibilités en un seul endroit.</p>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition group">
                    <div class="w-14 h-14 bg-green-500/20 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-green-500/30 transition">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Loueurs vérifiés</h3>
                    <p class="text-white/50 text-sm leading-relaxed">Chaque partenaire est vérifié et noté par notre communauté. Louez en toute confiance partout en Algérie.</p>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition group">
                    <div class="w-14 h-14 bg-green-500/20 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-green-500/30 transition">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Location & Transfert</h3>
                    <p class="text-white/50 text-sm leading-relaxed">Louez un véhicule en libre-service ou réservez un transfert avec chauffeur. Deux services, une seule plateforme.</p>
                </div>

                {{-- Card 4 --}}
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition group">
                    <div class="w-14 h-14 bg-green-500/20 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-green-500/30 transition">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Partout en Algérie</h3>
                    <p class="text-white/50 text-sm leading-relaxed">D'Alger à Tamanrasset, d'Oran à Annaba. Trouvez un véhicule ou un chauffeur dans toutes les wilayas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section fusionnée : Voiture à l'arrivée / Transfert -->
    @include('front.components.arrival-section')

    <!-- Blog Section - Actualités -->
    @if(isset($blogPosts) && $blogPosts->count() > 0)
    <section class="py-16 lg:py-24 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-700 rounded-full"></div>
                        <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">ResaDZ Magazine</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900">Actualités</h2>
                    <p class="mt-2 text-gray-500">Promotions, guides et conseils pour votre location</p>
                </div>
                <a href="{{ route('blog.index') }}" class="hidden sm:inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full transition">
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
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-full transition">
                    Voir toutes les actualités
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Loueurs Section -->
    @if($loueurs->count() > 0)
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                    <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Reseau</span>
                    <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">{{ $homeContent['loueurs_title'] ?? 'Nos loueurs partenaires' }}</h2>
                <p class="mt-2 text-gray-500">{{ $homeContent['loueurs_subtitle'] ?? 'Des professionnels verifies a votre service' }}</p>
            </div>

            <!-- Slider des partenaires -->
            <div class="relative">
                <div class="swiper partners-swiper">
                    <div class="swiper-wrapper pb-4">
                        @foreach($loueurs as $loueur)
                            <div class="swiper-slide">
                                <a href="{{ route('loueur.show', $loueur->slug) }}" class="group block bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-xl hover:border-green-200 transition-all hover:-translate-y-1 h-full">
                                    <div class="flex items-center gap-4">
                                        @if($loueur->logo)
                                            <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                                        @else
                                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex flex-col items-center justify-center shadow-sm">
                                                <span class="text-white font-black text-[10px] leading-none">PARTENAIRE</span>
                                                <span class="text-white font-black text-xs leading-tight">ResaDZ</span>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition truncate">{{ $loueur->company_name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $loueur->city ?? $loueur->wilaya ?? 'Algerie' }}</p>
                                            @if($loueur->total_reviews > 0)
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <div class="flex items-center gap-0.5">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="w-3.5 h-3.5 {{ $i <= round($loueur->rating) ? 'text-amber-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span class="text-sm font-semibold text-gray-700">{{ number_format($loueur->rating, 1) }}</span>
                                                    <span class="text-xs text-gray-400">({{ $loueur->total_reviews }})</span>
                                                </div>
                                            @else
                                                <p class="text-xs text-gray-400 mt-1">Nouveau partenaire</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-sm text-gray-500">{{ $loueur->vehicles_count }} vehicule{{ $loueur->vehicles_count > 1 ? 's' : '' }}</span>
                                        <div class="flex items-center gap-2">
                                            @if($loueur->rating >= 4.5 && $loueur->total_reviews >= 10)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full border border-amber-200">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    Top
                                                </span>
                                            @endif
                                            @if($loueur->is_verified)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Verifie
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="swiper-pagination partners-pagination"></div>
                </div>

                <!-- Navigation buttons -->
                <button class="partners-prev absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition hidden lg:flex">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button class="partners-next absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition hidden lg:flex">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Bouton Voir tous nos partenaires -->
            @if($totalFeaturedPartners > 5 || $totalLoueurs > 5)
            <div class="text-center mt-10">
                <a href="{{ route('loueurs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Voir tous nos partenaires
                </a>
            </div>
            @endif
        </div>
    </section>

    @endif

    <!-- SEO Section -->
    <section class="py-12 lg:py-24 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Main SEO Header --}}
            <div class="text-center mb-10 lg:mb-16">
                <h2 class="text-2xl lg:text-4xl font-black text-gray-900 leading-tight">
                    Location voiture Alger pas cher <span class="text-green-600">&ndash;</span> Transfert Aéroport & Chauffeur privé
                </h2>
                <p class="mt-3 lg:mt-4 text-gray-600 text-base lg:text-lg max-w-3xl mx-auto leading-relaxed">
                    Location voiture Alger, transfert aéroport Alger, chauffeur privé Alger : Resa DZ est la plateforme algérienne qui compare les offres des loueurs professionnels et facilite votre réservation en ligne, avec ou sans chauffeur.
                </p>
            </div>

            {{-- Two columns: Location + Transfert --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-8 mb-10 lg:mb-16">

                {{-- Location --}}
                <div class="bg-gray-50 rounded-2xl p-5 lg:p-8 border border-gray-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Louer une voiture à Alger : simple, rapide, comparatif</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-5">Vous cherchez une location de voiture à Alger pas cher ? Resa DZ centralise les offres disponibles :</p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Location voiture Aéroport Alger Houari Boumédiène
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Location voiture centre-ville Alger
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Location courte durée / longue durée
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Location voiture sans chauffeur
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Location voiture avec chauffeur
                        </li>
                    </ul>
                    <p class="mt-5 text-sm text-gray-500">Comparez prix, disponibilité et conditions en quelques clics.</p>
                </div>

                {{-- Transfert --}}
                <div class="bg-gray-50 rounded-2xl p-5 lg:p-8 border border-gray-100">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Transfert Aéroport Alger &ndash; Chauffeur privé</h3>
                    </div>
                    <p class="text-gray-600 text-sm mb-5">Besoin d'un transfert aéroport Alger fiable ? Resa DZ permet de réserver :</p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Transfert Aéroport &#10132; Centre-ville
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Chauffeur privé à l'heure
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Mise à disposition journée complète
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Transport VIP / Business
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Navette hôtels / événements
                        </li>
                    </ul>
                    <p class="mt-5 text-sm text-gray-500">Solution idéale pour voyageurs, professionnels et touristes.</p>
                </div>
            </div>

            {{-- Prix location voiture --}}
            <div class="mb-10 lg:mb-16">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-700 rounded-full"></div>
                    <h3 class="text-2xl font-black text-gray-900">Prix location voiture Alger (2026)</h3>
                </div>
                <p class="text-gray-500 text-sm mb-6">Les tarifs varient selon saison, catégorie et durée.</p>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                    <div class="bg-gray-50 rounded-xl p-3.5 lg:p-5 border border-gray-100 hover:border-green-200 transition">
                        <div class="text-xl lg:text-2xl mb-1.5 lg:mb-2">&#128663;</div>
                        <h4 class="font-bold text-gray-900 text-xs lg:text-base">Citadine économique</h4>
                        <p class="text-green-600 font-semibold mt-1 text-xs lg:text-sm">À partir de 4 000 DA / jour</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3.5 lg:p-5 border border-gray-100 hover:border-green-200 transition">
                        <div class="text-xl lg:text-2xl mb-1.5 lg:mb-2">&#128664;</div>
                        <h4 class="font-bold text-gray-900 text-xs lg:text-base">Compacte</h4>
                        <p class="text-green-600 font-semibold mt-1 text-xs lg:text-sm">Entre 5 000 et 6 500 DA / jour</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3.5 lg:p-5 border border-gray-100 hover:border-green-200 transition">
                        <div class="text-xl lg:text-2xl mb-1.5 lg:mb-2">&#128665;</div>
                        <h4 class="font-bold text-gray-900 text-xs lg:text-base">SUV / 4x4</h4>
                        <p class="text-green-600 font-semibold mt-1 text-xs lg:text-sm">Entre 6 500 et 9 000 DA / jour</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3.5 lg:p-5 border border-gray-100 hover:border-green-200 transition">
                        <div class="text-xl lg:text-2xl mb-1.5 lg:mb-2">&#128084;</div>
                        <h4 class="font-bold text-gray-900 text-xs lg:text-base">Berline premium</h4>
                        <p class="text-green-600 font-semibold mt-1 text-xs lg:text-sm">À partir de 10 000 DA / jour</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3.5 lg:p-5 border border-gray-100 hover:border-amber-200 transition">
                        <div class="text-xl lg:text-2xl mb-1.5 lg:mb-2">&#128662;</div>
                        <h4 class="font-bold text-gray-900 text-xs lg:text-base">Transfert avec chauffeur</h4>
                        <p class="text-amber-600 font-semibold mt-1 text-xs lg:text-sm">Tarif selon distance et durée</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Réserver à l'avance permet d'obtenir les meilleurs prix.
                </p>
            </div>

            {{-- Types de véhicules + Pourquoi Resa DZ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-8 mb-10 lg:mb-16">
                <div class="bg-gray-50 rounded-2xl p-5 lg:p-8 border border-gray-100">
                    <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-4">Types de véhicules disponibles à Alger</h3>
                    <ul class="space-y-2.5">
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                            <span><strong>Citadine</strong> &ndash; idéal circulation urbaine</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                            <span><strong>SUV & 4x4</strong> &ndash; confort et espace</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                            <span><strong>Berline affaires</strong></span>
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                            <span><strong>Véhicule premium</strong> avec chauffeur</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                            <span><strong>Utilitaire</strong> &ndash; selon disponibilité des loueurs</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-green-50 rounded-2xl p-5 lg:p-8 border border-green-100">
                    <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-4">Pourquoi choisir Resa DZ ?</h3>
                    <ul class="space-y-2.5">
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Plateforme spécialisée Algérie
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Mise en relation directe avec loueurs vérifiés
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Large choix de véhicules
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Réservation en ligne rapide
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Location avec ou sans chauffeur
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Service transfert aéroport Alger
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Comment réserver + Documents --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-8 mb-10 lg:mb-16">
                <div class="bg-neutral-950 rounded-2xl p-5 lg:p-8 text-white">
                    <h3 class="text-xl font-bold mb-6">Comment réserver une voiture à Alger ?</h3>
                    <ol class="space-y-5">
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">1</span>
                            <span class="text-white/80 text-sm pt-1">Sélectionnez lieu (Aéroport Alger, centre-ville, hôtel...)</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">2</span>
                            <span class="text-white/80 text-sm pt-1">Choisissez dates et horaires</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">3</span>
                            <span class="text-white/80 text-sm pt-1">Comparez les offres disponibles</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">4</span>
                            <span class="text-white/80 text-sm pt-1">Réservez directement via la plateforme</span>
                        </li>
                    </ol>
                </div>

                <div class="bg-gray-50 rounded-2xl p-5 lg:p-8 border border-gray-100">
                    <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-5">Documents nécessaires</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Permis de conduire valide</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Carte d'identité ou passeport</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Caution selon conditions du loueur</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- FAQ --}}
            <div>
                <div class="flex items-center gap-3 mb-6 lg:mb-8">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-500 to-green-700 rounded-full"></div>
                    <h3 class="text-2xl font-black text-gray-900">FAQ &ndash; Location voiture Alger</h3>
                </div>

                <div x-data="{ active: null }" class="space-y-3">
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-900 text-sm">Quelle est la voiture la moins chère à Alger ?</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="active === 1 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 1" x-collapse class="px-6 pb-4">
                            <p class="text-sm text-gray-600">Les citadines économiques démarrent autour de 4 000 DA par jour selon disponibilité.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-900 text-sm">Peut-on louer une voiture à l'aéroport d'Alger ?</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="active === 2 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 2" x-collapse class="px-6 pb-4">
                            <p class="text-sm text-gray-600">Oui, plusieurs loueurs proposent la livraison et la prise en charge à l'aéroport.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-900 text-sm">Peut-on réserver un chauffeur privé à Alger ?</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="active === 3 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 3" x-collapse class="px-6 pb-4">
                            <p class="text-sm text-gray-600">Oui, Resa DZ permet de réserver un transfert ou une mise à disposition avec chauffeur.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="active = active === 4 ? null : 4" class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-900 text-sm">La location inclut-elle l'assurance ?</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="active === 4 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="active === 4" x-collapse class="px-6 pb-4">
                            <p class="text-sm text-gray-600">Cela dépend du loueur. Les conditions sont précisées sur chaque annonce.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    // Partners Slider
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.partners-swiper')) {
            new Swiper('.partners-swiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                pagination: {
                    el: '.partners-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.partners-next',
                    prevEl: '.partners-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        }
    });

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
            autoSlideInterval = setInterval(nextSlide, 6000);
        }

        autoSlideInterval = setInterval(nextSlide, 6000);
    });
</script>
@endsection
