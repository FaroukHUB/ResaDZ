@php
    $siteName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $siteSlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
    $siteDescription = \App\Models\Setting::get('company_tagline', 'Marketplace de location de voitures');

    // Réseaux sociaux
    $socialFacebook = \App\Models\Setting::get('facebook', '');
    $socialInstagram = \App\Models\Setting::get('instagram', '');
    $socialWhatsapp = \App\Models\Setting::get('whatsapp', '');

    // Logos
    $logoLight = \App\Models\Setting::get('logo_light', '');
    $logoDark = \App\Models\Setting::get('logo_dark', '');
@endphp
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $siteName . ' - ' . $siteSlogan)</title>
    <meta name="description" content="@yield('meta_description', $siteName . ', la marketplace de location de voitures en Algérie. Trouvez et réservez votre véhicule en quelques clics.')">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', $siteName . ' - ' . $siteSlogan)">
    <meta property="og:description" content="@yield('og_description', 'Trouvez et réservez votre véhicule en quelques clics.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('assets/favicon.png'))">
    <meta property="og:locale" content="fr_DZ">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">

    @yield('meta_extra')

    <!-- Canonical -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS + App -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')
</head>
<body class="bg-white text-gray-900 antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @if($logoLight)
                        <img src="{{ Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-28 w-auto" width="140" height="112">
                    @else
                        {{-- Fallback text logo --}}
                        <span class="text-2xl font-black text-gray-900">{{ $siteName }}</span>
                    @endif
                </a>

                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Accueil</a>
                    <a href="{{ route('vehicles.index') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Véhicules</a>
                    <a href="{{ route('comment-ca-marche') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Comment ça marche</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-3">
                    @auth
                        @if(Auth::user()->loueur)
                            <a href="/loueur" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                                Mon espace
                            </a>
                        @elseif(Auth::user()->role === 'admin')
                            <a href="/admin" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition text-sm">
                                Admin
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" aria-label="Déconnexion" class="text-sm text-gray-400 hover:text-gray-600 transition min-w-[44px] min-h-[44px] inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            Connexion
                        </a>
                        {{-- S'inscrire dropdown --}}
                        <div class="relative hidden sm:block" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow-sm">
                                S'inscrire
                                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                                <a href="{{ route('register') }}?type=loueur" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">Loueur</div>
                                        <div class="text-xs text-gray-500">Location de véhicules</div>
                                    </div>
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <a href="{{ route('register') }}?type=taxi" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">Chauffeur / Taxi</div>
                                        <div class="text-xs text-gray-500">Transfert & livraison</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" aria-label="Ouvrir le menu" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 min-w-[44px] min-h-[44px] inline-flex items-center justify-center" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col gap-2">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Accueil</a>
                    <a href="{{ route('vehicles.index') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Véhicules</a>
                    <a href="{{ route('comment-ca-marche') }}" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Comment ça marche</a>
                    <div class="border-t border-gray-100 my-2"></div>
                    @auth
                        @if(Auth::user()->loueur)
                            <a href="/loueur" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Mon espace loueur</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 font-medium">Déconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg bg-gray-900 text-white text-center font-medium">Connexion</a>
                        <a href="{{ route('register') }}?type=loueur" class="px-3 py-2 rounded-lg bg-green-600 text-white text-center font-medium">S'inscrire en tant que Loueur</a>
                        <a href="{{ route('register') }}?type=taxi" class="px-3 py-2 rounded-lg bg-amber-600 text-white text-center font-medium">S'inscrire en tant que Chauffeur</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Ultra Modern -->
    <footer class="relative bg-black overflow-hidden">
        {{-- Decorative Elements --}}
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-red-500/5 rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 50px 50px;"></div>
        </div>

        {{-- Top Border Gradient --}}
        <div class="h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-50"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Main Footer Content --}}
            <div class="py-16 lg:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
                    {{-- Brand Column --}}
                    <div class="lg:col-span-4">
                        <div class="flex items-center gap-3 mb-6">
                            @if($logoDark)
                                <img src="{{ Storage::url($logoDark) }}" alt="{{ $siteName }}" class="h-32 w-auto" width="160" height="128">
                            @elseif($logoLight)
                                <img src="{{ Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-32 w-auto brightness-0 invert" width="160" height="128">
                            @else
                                <span class="text-3xl font-black text-white">{{ $siteName }}</span>
                            @endif
                        </div>
                        <p class="text-white/60 leading-relaxed mb-8">La marketplace de location de voitures en Algérie. Trouvez le véhicule idéal auprès de loueurs professionnels vérifiés.</p>

                        {{-- Social Links --}}
                        <div class="flex items-center gap-3">
                            @if($socialFacebook)
                            <a href="{{ $socialFacebook }}" target="_blank" rel="noopener" aria-label="Facebook" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-blue-600 text-white/50 hover:text-white transition border border-white/10">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                            </a>
                            @endif
                            @if($socialInstagram)
                            <a href="{{ $socialInstagram }}" target="_blank" rel="noopener" aria-label="Instagram" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-gradient-to-br hover:from-purple-600 hover:to-pink-500 text-white/50 hover:text-white transition border border-white/10">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            @endif
                            @if($socialWhatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $socialWhatsapp) }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-green-600 text-white/50 hover:text-white transition border border-white/10">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Navigation Links --}}
                    <div class="lg:col-span-2">
                        <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-6">Navigation</h4>
                        <ul class="space-y-4">
                            <li><a href="{{ route('home') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Accueil</a></li>
                            <li><a href="{{ route('vehicles.index') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Véhicules</a></li>
                            <li><a href="{{ route('blog.index') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Actualités</a></li>
                            <li><a href="{{ route('comment-ca-marche') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Comment ça marche</a></li>
                        </ul>
                    </div>

                    {{-- Wilayas Populaires --}}
                    <div class="lg:col-span-2">
                        <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-6">Wilayas populaires</h4>
                        <ul class="space-y-4">
                            <li><a href="{{ route('vehicles.by-wilaya', 'alger') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Alger</a></li>
                            <li><a href="{{ route('vehicles.by-wilaya', 'oran') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Oran</a></li>
                            <li><a href="{{ route('vehicles.by-wilaya', 'constantine') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Constantine</a></li>
                            <li><a href="{{ route('vehicles.by-wilaya', 'annaba') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Annaba</a></li>
                        </ul>
                    </div>

                    {{-- Newsletter --}}
                    <div class="lg:col-span-4">
                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold">Newsletter</h4>
                                    <p class="text-white/40 text-xs">Offres exclusives & bons plans</p>
                                </div>
                            </div>
                            <x-newsletter-footer />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Trust Badges --}}
            <div class="border-t border-white/10 py-8">
                <div class="flex flex-wrap items-center justify-center gap-8 lg:gap-12">
                    <div class="flex items-center gap-3 text-white/40">
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="text-sm">Paiements sécurisés</span>
                    </div>
                    <div class="flex items-center gap-3 text-white/40">
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm">Loueurs vérifiés</span>
                    </div>
                    <div class="flex items-center gap-3 text-white/40">
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="text-sm">Support 7j/7</span>
                    </div>
                    <div class="flex items-center gap-3 text-white/40">
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-sm">Partout en Algérie</span>
                    </div>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-white/10 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-white/60 text-sm">&copy; {{ date('Y') }} {{ $siteName }}. Tous droits réservés.</p>
                    <div class="flex items-center gap-6 text-white/60 text-sm">
                        <a href="#" class="hover:text-white transition">Mentions légales</a>
                        <a href="#" class="hover:text-white transition">CGU</a>
                        <a href="#" class="hover:text-white transition">Confidentialité</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')

    {{-- Analytics Tracking --}}
    <script defer src="{{ asset('js/tracking.js') }}"></script>

    {{-- Popup Component --}}
    <x-popup :page-type="$pageType ?? null" />

    {{-- Lead Capture Popup --}}
    <x-lead-capture />
</body>
</html>
