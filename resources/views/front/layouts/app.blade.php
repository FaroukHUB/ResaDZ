<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ResaDZ - Location de voitures en Algérie')</title>
    <meta name="description" content="@yield('meta_description', 'ResaDZ, la marketplace de location de voitures en Algérie. Trouvez et réservez votre véhicule en quelques clics.')">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'ResaDZ - Location de voitures en Algérie')">
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
                        <img src="{{ asset('assets/logo.png') }}" alt="ResaDZ" class="h-10 w-auto">
                    @elseif(file_exists(public_path('assets/logo.jpeg')))
                        <img src="{{ asset('assets/logo.jpeg') }}" alt="ResaDZ" class="h-10 w-auto">
                    @else
                        {{-- Fallback text logo --}}
                        <span class="text-2xl font-black text-gray-900">Resa<span class="text-red-600">DZ</span></span>
                    @endif
                </a>

                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Accueil</a>
                    <a href="{{ route('vehicles.index') }}" class="text-gray-700 hover:text-red-600 transition font-medium">Véhicules</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-red-600 transition font-medium">Comment ça marche</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('vehicles.index') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-sm">
                        Réserver
                    </a>
                    @auth
                        <a href="/admin" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a>
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
                    <a href="#how-it-works" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Comment ça marche</a>
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
                            <img src="{{ asset('assets/logo.png') }}" alt="ResaDZ" class="h-10 w-auto brightness-0 invert">
                        @elseif(file_exists(public_path('assets/logo.jpeg')))
                            <img src="{{ asset('assets/logo.jpeg') }}" alt="ResaDZ" class="h-10 w-auto brightness-0 invert">
                        @else
                            <span class="text-2xl font-black text-white">Resa<span class="text-red-500">DZ</span></span>
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

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li>contact@resadz.com</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-10 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} ResaDZ. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
