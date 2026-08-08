@php
    $siteName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $siteSlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
    $siteDescription = \App\Models\Setting::get('company_tagline', 'Marketplace de location de voitures');

    // Réseaux sociaux
    $socialFacebook = \App\Models\Setting::get('facebook', '');
    $socialInstagram = \App\Models\Setting::get('instagram', '');
    $socialTiktok = \App\Models\Setting::get('tiktok', 'https://www.tiktok.com/@resadzalger');
    $googleReviewsUrl = \App\Models\Setting::get('google_reviews_url', '');
    $googleRating = (float) \App\Models\Setting::get('google_rating', 4.5);
    $socialWhatsapp = \App\Models\Setting::get('whatsapp', '');

    // Logos
    $logoLight = \App\Models\Setting::get('logo_light', '');
    $logoDark = \App\Models\Setting::get('logo_dark', '');

    // Favicon (utilise le favicon uploadé, sinon le logo light, sinon le fallback)
    $faviconSetting = \App\Models\Setting::get('favicon', '');
    $faviconUrl = $faviconSetting ? Storage::url($faviconSetting) : ($logoLight ? Storage::url($logoLight) : $faviconUrl);
@endphp
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $siteName . ' — Location de voitures entre particuliers en Algérie')</title>
    <meta name="description" content="@yield('meta_description', 'ResaDZ, la première marketplace de location de voitures en Algérie. Comparez les prix, réservez en ligne et payez par CB. Loueurs vérifiés dans les 58 wilayas.')">

    <!-- SEO Meta Tags -->
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <meta name="author" content="{{ $siteName }}">
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="format-detection" content="telephone=no">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', $siteName . ' - ' . $siteSlogan)">
    <meta property="og:description" content="@yield('og_description', 'Trouvez et réservez votre véhicule en quelques clics.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', $faviconUrl)">
    <meta property="og:locale" content="fr_DZ">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', $siteName . ' - ' . $siteSlogan)">
    <meta name="twitter:description" content="@yield('og_description', 'Trouvez et réservez votre véhicule en quelques clics.')">
    <meta name="twitter:image" content="@yield('og_image', $faviconUrl)">

    <!-- Geographic tags for Algeria -->
    <meta name="geo.region" content="DZ">
    <meta name="geo.placename" content="Algérie">

    @yield('meta_extra')

    <!-- Canonical -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">

    <!-- Fonts - Non-blocking avec display=swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap"></noscript>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.3/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- Tailwind CSS + App -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')

    <style>
    @keyframes offerBounce {
        0%, 100% { transform: translateY(0) scale(1); }
        25% { transform: translateY(-4px) scale(1.05); }
        50% { transform: translateY(0) scale(1); }
        75% { transform: translateY(-2px) scale(1.02); }
    }
    </style>

    {{-- Meta Pixel (Facebook) --}}
    @php $fbPixelId = \App\Models\Setting::get('facebook_pixel_id', ''); @endphp
    @if($fbPixelId)
    <script>
    // Pixel chargé uniquement après consentement cookies
    window.resadzLoadPixel = function() {
        if (window._resadzPixelLoaded) return;
        window._resadzPixelLoaded = true;
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init','{{ $fbPixelId }}');
        fbq('track','PageView');
    };
    try { if (localStorage.getItem('resadz_cookie_consent') === 'accepted') { window.resadzLoadPixel(); } } catch (e) {}
    </script>
    @endif
</head>
<body class="bg-white text-gray-900 antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3" style="flex-shrink:0;">
                    @if($logoLight)
                        <img src="{{ Storage::url($logoLight) }}" alt="{{ $siteName }}" class="hdr-logo" width="140" height="112">
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
                    {{-- Réseaux sociaux + Avis Google (styles inline : indépendant du build Tailwind) --}}
                    <style>
                        .hdr-logo { height: 92px; width: auto; }
                        @media (min-width: 1024px) { .hdr-logo { height: 112px; } }
                        .hdr-social-wrap { display: flex; align-items: center; gap: 4px; margin-right: 2px; }
                        .hdr-social-btn { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 9999px; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.15); transition: opacity .2s; }
                        .hdr-social-btn svg { width: 13px; height: 13px; }
                        .hdr-social-btn:hover { opacity: .8; }
                        .hdr-greviews { display: flex; flex-direction: column; align-items: center; gap: 1px; padding: 0 6px; border-left: 1px solid #e5e7eb; text-decoration: none; transition: opacity .2s; }
                        .hdr-greviews:hover { opacity: .8; }
                        .hdr-grev-top { display: flex; align-items: center; gap: 3px; }
                        .hdr-greviews-glogo { width: 18px; height: 18px; }
                        .hdr-grev-note { font-size: 12px; font-weight: 700; color: #111827; }
                        .hdr-grev-stars { display: flex; align-items: center; gap: 1px; }
                        .hdr-grev-stars svg { width: 10px; height: 10px; }
                        .hdr-greviews-label { display: none; }
                        .hdr-user-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 9999px; transition: opacity .2s; flex-shrink: 0; }
                        .hdr-user-btn svg { width: 17px; height: 17px; }
                        @media (min-width: 1024px) {
                            .hdr-social-wrap { gap: 10px; margin-right: 8px; }
                            .hdr-social-btn { width: 36px; height: 36px; }
                            .hdr-social-btn svg { width: 20px; height: 20px; }
                            .hdr-greviews { gap: 2px; padding: 0 14px; border-right: 1px solid #e5e7eb; }
                            .hdr-greviews-glogo { width: 26px; height: 26px; }
                            .hdr-grev-note { font-size: 14px; }
                            .hdr-grev-stars svg { width: 14px; height: 14px; }
                            .hdr-greviews-label { display: block; font-size: 13px; font-weight: 700; color: #111827; }
                            .hdr-user-btn { width: 38px; height: 38px; }
                            .hdr-user-btn svg { width: 22px; height: 22px; }
                        }
                    </style>
                    <div class="hdr-social-wrap">
                        @if($socialFacebook)
                        <a href="{{ $socialFacebook }}" target="_blank" rel="noopener" aria-label="Facebook" class="hdr-social-btn" style="background:#1877F2;">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($socialInstagram)
                        <a href="{{ $socialInstagram }}" target="_blank" rel="noopener" aria-label="Instagram" class="hdr-social-btn" style="background:radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if($socialTiktok)
                        <a href="{{ $socialTiktok }}" target="_blank" rel="noopener" aria-label="TikTok" class="hdr-social-btn" style="background:#000;">
                            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                        @endif

                        {{-- Avis Google --}}
                        @if($googleReviewsUrl)
                        <a href="{{ $googleReviewsUrl }}" target="_blank" rel="noopener" class="hdr-greviews" aria-label="Avis Google">
                            <span class="hdr-grev-top">
                                <svg class="hdr-greviews-glogo" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>
                                <span class="hdr-grev-note">{{ number_format($googleRating, 1) }}</span>
                                <span class="hdr-greviews-label">Avis Google</span>
                            </span>
                            <span class="hdr-grev-stars">
                                @php $fullStars = (int) floor($googleRating); $halfStar = ($googleRating - $fullStars) >= 0.5; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <svg fill="currentColor" style="color:#FBBC04;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @elseif($i == $fullStars + 1 && $halfStar)
                                        <svg viewBox="0 0 20 20"><defs><linearGradient id="hdrhalfstar"><stop offset="50%" stop-color="#FBBC04"/><stop offset="50%" stop-color="#E5E7EB"/></linearGradient></defs><path fill="url(#hdrhalfstar)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @else
                                        <svg fill="currentColor" style="color:#D1D5DB;" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endif
                                @endfor
                            </span>
                        </a>
                        @endif
                    </div>
                    @auth
                        @php
                            $espaceUrl = Auth::user()->loueur ? '/loueur' : (in_array(Auth::user()->role, ['admin', 'super_admin']) ? '/admin' : url('/'));
                        @endphp
                        <a href="{{ $espaceUrl }}" aria-label="Mon compte" title="Connecté — Mon espace" class="hdr-user-btn" style="background:#DCFCE7;">
                            <svg style="color:#16A34A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </a>
                        @if(Auth::user()->loueur)
                            <a href="/loueur" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                                Mon espace
                            </a>
                        @elseif(in_array(Auth::user()->role, ['admin', 'super_admin']))
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
                        <a href="{{ route('login') }}" aria-label="Connexion" title="Connexion" class="hdr-user-btn" style="background:#FEE2E2;">
                            <svg style="color:#DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </a>
                        {{-- S'inscrire dropdown --}}
                        <div class="relative hidden sm:block" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                    :aria-expanded="open"
                                    aria-haspopup="true"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow-sm">
                                S'inscrire
                                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                            @if($socialTiktok)
                            <a href="{{ $socialTiktok }}" target="_blank" rel="noopener" aria-label="TikTok" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-black text-white/50 hover:text-white transition border border-white/10">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
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
                        <h2 class="text-white font-bold text-sm uppercase tracking-wider mb-6">Navigation</h2>
                        <ul class="space-y-4">
                            <li><a href="{{ route('home') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Accueil</a></li>
                            <li><a href="{{ route('vehicles.index') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Véhicules</a></li>
                            <li><a href="{{ route('blog.index') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Actualités</a></li>
                            <li><a href="{{ route('comment-ca-marche') }}" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Comment ça marche</a></li>
                        </ul>
                    </div>

                    {{-- Wilayas Populaires --}}
                    <div class="lg:col-span-2">
                        <h2 class="text-white font-bold text-sm uppercase tracking-wider mb-6">Wilayas populaires</h2>
                        <ul class="space-y-4">
                            <li><a href="/location-voiture-alger" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Alger</a></li>
                            <li><a href="/location-voiture-aeroport-alger" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location aéroport Alger</a></li>
                            <li><a href="/location-voiture-oran" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Oran</a></li>
                            <li><a href="/location-voiture-constantine" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Constantine</a></li>
                            <li><a href="/location-voiture-annaba" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Annaba</a></li>
                            <li><a href="/location-voiture-boumerdes" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Boumerdès</a></li>
                            <li><a href="/location-voiture-blida" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Blida</a></li>
                            <li><a href="/location-voiture-setif" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Sétif</a></li>
                            <li><a href="/location-voiture-sidi-bel-abbes" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Sidi Bel Abbès</a></li>
                            <li><a href="/location-voiture-tizi-ouzou" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Tizi Ouzou</a></li>
                            <li><a href="/location-voiture-bejaia" class="text-white/60 hover:text-green-400 transition flex items-center gap-2 group"><span class="w-1.5 h-1.5 rounded-full bg-white/20 group-hover:bg-green-400 transition"></span>Location voiture Béjaïa</a></li>
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
                                    <h2 class="text-white font-bold text-base">Newsletter</h2>
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
                    <p class="text-white/60 text-sm">Propulsé par <a href="https://mon-agenceweb.fr/" target="_blank" rel="noopener" class="font-semibold text-green-400 hover:text-green-300 transition underline underline-offset-2">Farouk</a></p>
                    <div class="flex items-center gap-6 text-white/60 text-sm">
                        <a href="{{ route('legal.mentions-legales') }}" class="hover:text-white transition">Mentions légales</a>
                        <a href="{{ route('legal.cgu') }}" class="hover:text-white transition">CGU</a>
                        <a href="{{ route('legal.confidentialite') }}" class="hover:text-white transition">Confidentialité</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')

    {{-- Bannière de consentement cookies --}}
    <div id="resadz-cookie-banner" style="display:none;position:fixed;bottom:16px;left:16px;right:16px;max-width:520px;margin:0 auto;z-index:9998;background:#111827;color:#fff;border-radius:16px;padding:20px;box-shadow:0 10px 40px rgba(0,0,0,.35);">
        <p style="font-weight:700;font-size:15px;margin:0 0 6px;">🍪 Cookies</p>
        <p style="font-size:13px;line-height:1.5;color:rgba(255,255,255,.75);margin:0 0 14px;">
            Nous utilisons des cookies pour mesurer l'audience et améliorer votre expérience (statistiques, publicité).
            Vous pouvez accepter ou refuser — le site fonctionne dans les deux cas.
            <a href="{{ route('legal.confidentialite') }}" style="color:#4ADE80;text-decoration:underline;">En savoir plus</a>
        </p>
        <div style="display:flex;gap:10px;">
            <button type="button" onclick="resadzCookieChoice('accepted')"
                    style="flex:1;padding:10px;border:none;border-radius:10px;background:#16A34A;color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                Accepter
            </button>
            <button type="button" onclick="resadzCookieChoice('refused')"
                    style="flex:1;padding:10px;border:1px solid rgba(255,255,255,.25);border-radius:10px;background:transparent;color:#fff;font-weight:600;font-size:14px;cursor:pointer;">
                Refuser
            </button>
        </div>
    </div>
    <script>
    (function() {
        try {
            if (!localStorage.getItem('resadz_cookie_consent')) {
                document.getElementById('resadz-cookie-banner').style.display = 'block';
            }
        } catch (e) {}
    })();
    function resadzCookieChoice(choice) {
        try { localStorage.setItem('resadz_cookie_consent', choice); } catch (e) {}
        document.getElementById('resadz-cookie-banner').style.display = 'none';
        if (choice === 'accepted' && window.resadzLoadPixel) { window.resadzLoadPixel(); }
    }
    </script>

    {{-- Analytics Tracking --}}
    <script defer src="{{ asset('js/tracking.js') }}"></script>

    {{-- Popup Component --}}
    <x-popup :page-type="$pageType ?? null" />

    {{-- Lead Capture Popup --}}
    <x-lead-capture />

    {{-- Chatbot Résabot --}}
    @include('partials.chatbot')
</body>
</html>
