@extends('front.layouts.app')

@section('title', $loueur->company_name . ' - Location de voitures | ResaDZ')
@section('meta_description', $loueur->meta_description ?? $loueur->company_name . ' - Location de voitures à ' . ($loueur->city ?? 'en Algérie') . '. Réservez en ligne sur ResaDZ.')

@section('meta_extra')
@php
$schemaData = [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $loueur->company_name,
    'description' => $loueur->description ?? 'Location de voitures',
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => $loueur->city ?? '',
        'addressRegion' => $loueur->wilaya ?? '',
        'addressCountry' => 'DZ',
    ],
    'url' => url()->current(),
];
if ($loueur->total_reviews > 0) {
    $schemaData['aggregateRating'] = [
        '@type' => 'AggregateRating',
        'ratingValue' => number_format($loueur->rating, 1),
        'reviewCount' => $loueur->total_reviews,
    ];
}
@endphp
<script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endsection

@section('content')

    {{-- Cover Image --}}
    @if($loueur->cover_image)
        <div class="relative h-48 md:h-64 lg:h-80 bg-gray-900">
            <img src="{{ asset('storage/' . $loueur->cover_image) }}" alt="{{ $loueur->company_name }}" class="w-full h-full object-cover opacity-90">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
        </div>
    @else
        <div class="h-32 md:h-40 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900"></div>
    @endif

    <!-- Hero -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $loueur->cover_image ? '-mt-16 md:-mt-20' : '' }} pb-8 pt-6">
            <div class="flex flex-col sm:flex-row items-start gap-6">
                {{-- Logo --}}
                @if($loueur->logo)
                    <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-24 h-24 md:w-28 md:h-28 rounded-2xl object-cover shadow-lg border-4 border-white bg-white">
                @else
                    {{-- Logo par défaut stylé Partenaire ResaDZ --}}
                    <div class="w-24 h-24 md:w-28 md:h-28 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex flex-col items-center justify-center shadow-lg border-4 border-white">
                        <span class="text-black font-black text-xs leading-none">PARTENAIRE</span>
                        <span class="text-black font-black text-lg leading-tight">ResaDZ</span>
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $loueur->company_name }}</h1>
                        @if($loueur->is_verified)
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-green-700 bg-green-50 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Vérifié
                            </span>
                        @endif
                    </div>

                    {{-- Location --}}
                    <p class="text-gray-500 mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $loueur->city ?? '' }} {{ $loueur->wilaya ? '- ' . $loueur->wilaya : '' }}
                    </p>

                    {{-- Rating --}}
                    @if($loueur->total_reviews > 0)
                        <div class="flex items-center gap-2 mt-3">
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($loueur->rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="font-bold text-gray-900 text-lg">{{ number_format($loueur->rating, 1) }}</span>
                            <a href="#avis" class="text-sm text-gray-500 hover:text-red-600 transition">({{ $loueur->total_reviews }} avis)</a>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mt-3">Nouveau partenaire - pas encore d'avis</p>
                    @endif

                    {{-- Description --}}
                    @if($loueur->description)
                        <p class="text-gray-600 mt-4 max-w-2xl">{{ $loueur->description }}</p>
                    @endif

                    {{-- Réseaux sociaux --}}
                    @if($loueur->facebook || $loueur->instagram || $loueur->tiktok)
                        <div class="flex flex-wrap gap-2 mt-4">
                            @if($loueur->facebook)
                                <a href="{{ $loueur->facebook }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    Facebook
                                </a>
                            @endif
                            @if($loueur->instagram)
                                <a href="https://instagram.com/{{ ltrim($loueur->instagram, '@') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    Instagram
                                </a>
                            @endif
                            @if($loueur->tiktok)
                                <a href="https://tiktok.com/@{{ ltrim($loueur->tiktok, '@') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                                    TikTok
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Stats & Badges Row --}}
            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Membre depuis --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $loueur->created_at->diffForHumans(null, true) }}</div>
                    <div class="text-sm text-gray-500">Membre depuis</div>
                </div>

                {{-- Véhicules --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $vehicles->total() }}</div>
                    <div class="text-sm text-gray-500">Véhicule{{ $vehicles->total() > 1 ? 's' : '' }}</div>
                </div>

                {{-- Locations --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $loueur->total_rentals ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Location{{ ($loueur->total_rentals ?? 0) > 1 ? 's' : '' }}</div>
                </div>

                {{-- Avis --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $loueur->total_reviews ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Avis client{{ ($loueur->total_reviews ?? 0) > 1 ? 's' : '' }}</div>
                </div>
            </div>

            {{-- Badges --}}
            @php $badges = $loueur->getBadges(); @endphp
            @if(count($badges) > 0)
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach($badges as $badge)
                        @php
                            $colors = [
                                'green' => 'bg-green-50 text-green-700 border-green-200',
                                'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'red' => 'bg-red-50 text-red-700 border-red-200',
                            ];
                            $colorClass = $colors[$badge['color']] ?? $colors['green'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full border {{ $colorClass }}">
                            @switch($badge['icon'])
                                @case('check')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @break
                                @case('truck')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    @break
                                @case('plane')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    @break
                                @case('arrow-down')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                    @break
                                @case('infinity')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.781 0-4.781 8 0 8 5.606 0 7.644-8 12.74-8z"/></svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endswitch
                            {{ $badge['text'] }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Payment Methods --}}
            @if($loueur->payment_methods && count($loueur->payment_methods) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Moyens de paiement acceptés</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($loueur->payment_methods as $method)
                            @php
                                $methodLabels = [
                                    'cash' => ['label' => 'Espèces', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                                    'ccp' => ['label' => 'CCP', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                                    'baridimob' => ['label' => 'BaridiMob', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                    'virement' => ['label' => 'Virement bancaire', 'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'],
                                    'paypal' => ['label' => 'PayPal', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                                    'wise' => ['label' => 'Wise', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ];
                                $methodInfo = $methodLabels[$method] ?? ['label' => ucfirst($method), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $methodInfo['icon'] }}"/></svg>
                                {{ $methodInfo['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Vehicles -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Véhicules disponibles
            <span class="text-gray-400 font-normal text-lg">({{ $vehicles->total() }})</span>
        </h2>

        @if($vehicles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($vehicles as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $vehicles->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Aucun véhicule disponible pour le moment.</p>
            </div>
        @endif

        {{-- Reviews Section --}}
        <div id="avis">
            @include('front.components.reviews-section', ['loueur' => $loueur])
        </div>
    </section>

@endsection
