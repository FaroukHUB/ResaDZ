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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    @yield('head')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">R</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Resa<span class="text-amber-600">DZ</span></span>
                </a>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-amber-600 transition font-medium">Accueil</a>
                    <a href="{{ route('vehicles.index') }}" class="text-gray-600 hover:text-amber-600 transition font-medium">Véhicules</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-amber-600 transition font-medium">Comment ça marche</a>
                </div>

                <!-- CTA -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('vehicles.index') }}" class="hidden sm:inline-flex items-center px-4 py-2 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 transition shadow-sm">
                        Réserver maintenant
                    </a>
                    @auth
                        <a href="/admin" class="text-sm text-gray-500 hover:text-gray-700">Dashboard</a>
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
    <footer class="bg-gray-900 text-gray-400 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">R</span>
                        </div>
                        <span class="text-xl font-bold text-white">Resa<span class="text-amber-500">DZ</span></span>
                    </div>
                    <p class="text-gray-400 max-w-sm">La marketplace de location de voitures en Algérie. Trouvez le véhicule idéal auprès de loueurs vérifiés.</p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Navigation</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="hover:text-amber-500 transition">Accueil</a></li>
                        <li><a href="{{ route('vehicles.index') }}" class="hover:text-amber-500 transition">Véhicules</a></li>
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
