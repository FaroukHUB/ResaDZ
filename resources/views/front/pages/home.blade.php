@extends('front.layouts.app')

@section('title', \App\Models\Setting::get('company_name', 'ResaDZ') . ' - ' . \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie'))
@section('meta_description', 'Marketplace de location de voitures en Algérie. Comparez et réservez auprès de loueurs vérifiés partout en Algérie.')

@section('content')

    <!-- Hero Section with Slider -->
    <section class="relative min-h-[600px] lg:min-h-[700px] flex items-center">
        {{-- Background Slider --}}
        <div class="absolute inset-0 z-0">
            @if(isset($heroSlides) && $heroSlides->count() > 0)
                {{-- Slider Images --}}
                <div id="hero-slider" class="relative w-full h-full">
                    @foreach($heroSlides as $index => $slide)
                        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-index="{{ $index }}">
                            <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title ?? 'ResaDZ' }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>

                {{-- Slider Navigation Dots --}}
                @if($heroSlides->count() > 1)
                    <div class="absolute bottom-24 left-1/2 transform -translate-x-1/2 z-20 flex gap-2">
                        @foreach($heroSlides as $index => $slide)
                            <button onclick="goToSlide({{ $index }})" class="hero-dot w-3 h-3 rounded-full transition-all {{ $index === 0 ? 'bg-white scale-110' : 'bg-white/50 hover:bg-white/70' }}" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                @endif
            @elseif(file_exists(public_path('assets/hero.jpg')))
                <img src="{{ asset('assets/hero.jpg') }}" alt="Location de voitures en Algérie" class="w-full h-full object-cover">
            @elseif(file_exists(public_path('assets/hero.png')))
                <img src="{{ asset('assets/hero.png') }}" alt="Location de voitures en Algérie" class="w-full h-full object-cover">
            @else
                {{-- Fallback gradient if no image --}}
                <div class="w-full h-full bg-gradient-to-br from-gray-900 via-gray-800 to-black"></div>
            @endif
            {{-- Overlay for text readability --}}
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="max-w-3xl">
                {{-- Dynamic Titles that change with slides --}}
                @if(isset($heroSlides) && $heroSlides->count() > 0)
                    <div class="relative">
                        @foreach($heroSlides as $index => $slide)
                            <div class="hero-title transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0 absolute top-0 left-0' }}" data-index="{{ $index }}">
                                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                                    @if($slide->title)
                                        {{ $slide->title }}
                                        @if($slide->subtitle)
                                            <span class="block text-red-500">{{ $slide->subtitle }}</span>
                                        @endif
                                    @else
                                        Louez votre voiture
                                        <span class="block text-red-500">partout en Algérie</span>
                                    @endif
                                </h1>
                                @if($slide->button_text && $slide->button_url)
                                    <a href="{{ $slide->button_url }}" class="mt-6 inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition shadow-lg">
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
                        <span class="block text-red-500">{{ $homeContent['hero_subtitle'] ?? 'partout en Algérie' }}</span>
                    </h1>
                @endif
                <p class="mt-6 text-lg sm:text-xl text-gray-200 max-w-xl">
                    {{ $homeContent['hero_description'] ?? 'Comparez les offres de loueurs vérifiés et réservez en quelques clics. Le meilleur de la location auto en DZ.' }}
                </p>

                <!-- Search Form Card -->
                <div class="mt-10 bg-white rounded-2xl shadow-2xl p-6 lg:p-8">
                    <form action="{{ route('vehicles.index') }}" method="GET" id="search-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            {{-- Location --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Lieu de prise en charge</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <select name="wilaya" class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 appearance-none cursor-pointer">
                                        <option value="">Wilaya, ville...</option>
                                        @if(isset($wilayas))
                                            @foreach($wilayas as $wilaya)
                                                <option value="{{ $wilaya }}">{{ $wilaya }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            {{-- Pickup Date --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Date de départ</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <input type="date" name="pickup_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 cursor-pointer">
                                </div>
                            </div>

                            {{-- Return Date --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Date de retour</label>
                                <div class="relative">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <input type="date" name="return_date" value="{{ date('Y-m-d', strtotime('+4 days')) }}" class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 focus:ring-2 focus:ring-red-500 focus:border-red-500 cursor-pointer">
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div>
                                <button type="submit" class="w-full px-6 py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-600/30 flex items-center justify-center gap-2">
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
                        <div class="text-sm text-gray-300 mt-1">Véhicules</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ $totalLoueurs }}+</div>
                        <div class="text-sm text-gray-300 mt-1">Loueurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ $wilayas->count() }}+</div>
                        <div class="text-sm text-gray-300 mt-1">Wilayas</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notre sélection pour vous -->
    @if($selectedVehicles->count() > 0)
    <section class="py-20 bg-gradient-to-b from-amber-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-amber-100 rounded-lg">
                            <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $homeContent['selection_title'] ?? 'Notre sélection pour vous' }}</h2>
                    </div>
                    <p class="text-gray-500">{{ $homeContent['selection_subtitle'] ?? 'Les véhicules que nous recommandons' }}</p>
                </div>
                <a href="{{ route('vehicles.index') }}" class="text-amber-600 font-semibold hover:text-amber-700 transition flex items-center gap-1">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($selectedVehicles as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle, 'showSelectionBorder' => true])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Citadines - Véhicules économiques -->
    @if(isset($vehiclesByCategory['citadine']) && $vehiclesByCategory['citadine']->count() > 0)
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Citadines</h2>
                    </div>
                    <p class="text-gray-500">Économiques et pratiques pour la ville</p>
                </div>
                <a href="{{ route('vehicles.index', ['category' => 'citadine']) }}" class="text-green-600 font-semibold hover:text-green-700 transition flex items-center gap-1">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($vehiclesByCategory['citadine'] as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Berlines - Confort et élégance -->
    @if(isset($vehiclesByCategory['berline']) && $vehiclesByCategory['berline']->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">Berlines</h2>
                    </div>
                    <p class="text-gray-500">Confort et élégance pour vos trajets</p>
                </div>
                <a href="{{ route('vehicles.index', ['category' => 'berline']) }}" class="text-blue-600 font-semibold hover:text-blue-700 transition flex items-center gap-1">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($vehiclesByCategory['berline'] as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- SUV - Puissance et polyvalence -->
    @if(isset($vehiclesByCategory['suv']) && $vehiclesByCategory['suv']->count() > 0)
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900">SUV</h2>
                    </div>
                    <p class="text-gray-500">Puissance et polyvalence pour tous vos trajets</p>
                </div>
                <a href="{{ route('vehicles.index', ['category' => 'suv']) }}" class="text-red-600 font-semibold hover:text-red-700 transition flex items-center gap-1">
                    Voir tout
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($vehiclesByCategory['suv'] as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Loueurs Section -->
    @if($loueurs->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">{{ $homeContent['loueurs_title'] ?? 'Nos loueurs partenaires' }}</h2>
                <p class="mt-2 text-gray-500">{{ $homeContent['loueurs_subtitle'] ?? 'Des professionnels vérifiés à votre service' }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($loueurs as $loueur)
                    <a href="{{ route('loueur.show', $loueur->slug) }}" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-red-200 transition-all">
                        <div class="flex items-center gap-4">
                            @if($loueur->logo)
                                <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                            @else
                                <div class="w-14 h-14 bg-gray-900 rounded-xl flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($loueur->company_name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-900 group-hover:text-red-600 transition">{{ $loueur->company_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $loueur->city ?? $loueur->wilaya ?? 'Algérie' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $loueur->vehicles_count }} véhicule{{ $loueur->vehicles_count > 1 ? 's' : '' }}</span>
                            @if($loueur->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Vérifié
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="py-20 bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white">{{ $homeContent['cta_title'] ?? 'Vous êtes loueur de voitures ?' }}</h2>
            <p class="mt-4 text-gray-400 text-lg">{{ $homeContent['cta_description'] ?? 'Rejoignez ResaDZ et développez votre activité en ligne. Gérez vos véhicules, réservations et finances depuis un seul tableau de bord.' }}</p>
            <a href="/loueur" class="mt-8 inline-flex items-center px-8 py-4 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-xl">
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
            // Transition images
            slides.forEach((slide, i) => {
                slide.classList.toggle('opacity-100', i === index);
                slide.classList.toggle('opacity-0', i !== index);
            });

            // Transition titles
            titles.forEach((title, i) => {
                if (i === index) {
                    title.classList.remove('opacity-0', 'absolute', 'top-0', 'left-0');
                    title.classList.add('opacity-100');
                } else {
                    title.classList.remove('opacity-100');
                    title.classList.add('opacity-0', 'absolute', 'top-0', 'left-0');
                }
            });

            // Update dots
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

        // Start auto-sliding every 5 seconds
        autoSlideInterval = setInterval(nextSlide, 5000);
    });
</script>
@endsection
