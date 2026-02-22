@php
    $siteName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $siteSlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
    $siteDescription = \App\Models\Setting::get('company_tagline', 'Marketplace de location de voitures');
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
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        'resadz': {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Hide scrollbar for mobile sliders */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>

    @yield('head')
</head>
<body class="bg-white text-gray-900 antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    {{-- Logo image - placez votre logo dans public/assets/logo.png --}}
                    @if(file_exists(public_path('assets/logo.png')))
                        <img src="{{ asset('assets/logo.png') }}" alt="{{ $siteName }}" class="h-10 w-auto">
                    @elseif(file_exists(public_path('assets/logo.jpeg')))
                        <img src="{{ asset('assets/logo.jpeg') }}" alt="{{ $siteName }}" class="h-10 w-auto">
                    @else
                        {{-- Fallback text logo --}}
                        <span class="text-2xl font-black text-gray-900">{{ $siteName }}</span>
                    @endif
                </a>

                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Accueil</a>
                    <a href="{{ route('vehicles.index') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Véhicules</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('vehicles.index') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-sm">
                        Réserver
                    </a>
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
                            <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-800 transition text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            Connexion
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
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
                        <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg bg-red-600 text-white text-center font-medium">Créer un compte loueur</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        @if(file_exists(public_path('assets/logo.png')))
                            <img src="{{ asset('assets/logo.png') }}" alt="{{ $siteName }}" class="h-10 w-auto brightness-0 invert">
                        @elseif(file_exists(public_path('assets/logo.jpeg')))
                            <img src="{{ asset('assets/logo.jpeg') }}" alt="{{ $siteName }}" class="h-10 w-auto brightness-0 invert">
                        @else
                            <span class="text-2xl font-black text-white">{{ $siteName }}</span>
                        @endif
                    </div>
                    <p class="text-gray-400 max-w-sm">La marketplace de location de voitures en Algérie. Trouvez le véhicule idéal auprès de loueurs vérifiés.</p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Navigation</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="hover:text-red-500 transition">Accueil</a></li>
                        <li><a href="{{ route('vehicles.index') }}" class="hover:text-red-500 transition">Véhicules</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <x-newsletter-footer />
                </div>
            </div>

            <div class="border-t border-gray-800 mt-10 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')

    {{-- Analytics Tracking --}}
    <script src="{{ asset('js/tracking.js') }}"></script>

    {{-- Popup Component --}}
    <x-popup :page-type="$pageType ?? null" />

    {{-- Lead Capture Popup --}}
    <x-lead-capture />
</body>
</html>
