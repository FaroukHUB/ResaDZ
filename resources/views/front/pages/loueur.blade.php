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

    {{-- Bannière Hero --}}
    <div class="relative h-48 md:h-64 lg:h-72">
        @if($loueur->cover_image)
            <img src="{{ asset('storage/' . $loueur->cover_image) }}" alt="{{ $loueur->company_name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-900 via-green-900 to-gray-900"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
        {{-- Nom en bas à droite de la bannière --}}
        <div class="absolute bottom-4 right-6 md:bottom-6 md:right-8 text-right">
            <h1 class="text-2xl md:text-4xl font-black text-white" style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);">{{ $loueur->company_name }}</h1>
            <p class="text-white/80 text-sm mt-1">
                {{ $loueur->city ?? '' }} {{ $loueur->wilaya ? '- ' . $loueur->wilaya : '' }}
            </p>
        </div>
    </div>

    {{-- Section profil --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Logo chevauchant --}}
            <div class="flex items-end gap-4 -mt-10 md:-mt-14">
                @if($loueur->logo)
                    <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-20 h-20 md:w-28 md:h-28 rounded-2xl object-cover shadow-xl border-4 border-white bg-white flex-shrink-0">
                @else
                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-br from-green-600 to-green-700 rounded-2xl flex items-center justify-center shadow-xl border-4 border-white flex-shrink-0">
                        <span class="text-white font-black text-xl md:text-2xl">{{ strtoupper(substr($loueur->company_name, 0, 2)) }}</span>
                    </div>
                @endif
            </div>

            {{-- Infos rapides --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 py-4 text-sm text-gray-500">
                @if($loueur->total_reviews > 0)
                    <div class="flex items-center gap-1.5">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($loueur->rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="font-bold text-gray-900">{{ number_format($loueur->rating, 1) }}</span>
                        <a href="#avis" class="hover:text-green-600 transition">({{ $loueur->total_reviews }} avis)</a>
                    </div>
                @else
                    <span class="text-gray-400">Nouveau partenaire</span>
                @endif
                <span class="hidden sm:inline text-gray-300">|</span>
                <span>Membre depuis {{ $loueur->created_at->diffForHumans(null, true) }}</span>
                <span class="hidden sm:inline text-gray-300">|</span>
                <span>{{ $vehicles->total() }} véhicule{{ $vehicles->total() > 1 ? 's' : '' }}</span>
                <span class="hidden sm:inline text-gray-300">|</span>
                <span>{{ $loueur->total_rentals ?? 0 }} location{{ ($loueur->total_rentals ?? 0) > 1 ? 's' : '' }}</span>
                @php $responseTime = $loueur->getFormattedResponseTime(); @endphp
                @if($responseTime !== 'N/A')
                    <span class="hidden sm:inline text-gray-300">|</span>
                    <span class="text-green-600 font-medium">⚡ Répond en {{ $responseTime }}</span>
                @endif
            </div>

            {{-- Réseaux sociaux (seulement Facebook, Instagram, TikTok — PAS de contact direct) --}}
            @if($loueur->facebook || $loueur->instagram || $loueur->tiktok)
                <div class="flex flex-wrap items-center gap-2 pb-4">
                    @if($loueur->facebook)
                        <a href="{{ $loueur->facebook }}" target="_blank" rel="noopener" class="w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                        </a>
                    @endif
                    @if($loueur->instagram)
                        <a href="https://instagram.com/{{ ltrim($loueur->instagram, '@') }}" target="_blank" rel="noopener" class="w-9 h-9 bg-gradient-to-br from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white rounded-full flex items-center justify-center transition" title="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif
                    @if($loueur->tiktok)
                        <a href="https://tiktok.com/{{ ltrim($loueur->tiktok, '@') }}" target="_blank" rel="noopener" class="w-9 h-9 bg-black hover:bg-gray-800 text-white rounded-full flex items-center justify-center transition" title="TikTok">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Colonne gauche (2/3) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Badges automatiques --}}
                @if(count($autoBadges) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($autoBadges as $badge)
                            @php
                                $colors = [
                                    'green' => 'bg-green-50 text-green-700 border-green-200',
                                    'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
                                ];
                                $colorClass = $colors[$badge['color']] ?? $colors['green'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full border {{ $colorClass }}">
                                {{ $badge['icon'] }} {{ $badge['text'] }}
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- À propos --}}
                @if($loueur->description || ($loueur->specialites && count($loueur->specialites) > 0) || ($loueur->langues && count($loueur->langues) > 0))
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">À propos</h2>
                        @if($loueur->description)
                            <p class="text-gray-600 leading-relaxed">{{ $loueur->description }}</p>
                        @endif

                        @if($loueur->specialites && count($loueur->specialites) > 0)
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Spécialités</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($loueur->specialites as $spec)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg">{{ \App\Models\Loueur::SPECIALITES[$spec] ?? $spec }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($loueur->langues && count($loueur->langues) > 0)
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Langues parlées</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($loueur->langues as $lang)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg">{{ \App\Models\Loueur::LANGUES[$lang] ?? $lang }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($loueur->horaires)
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">Horaires</h3>
                                <p class="text-gray-600 text-sm">{{ $loueur->horaires }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Promotions en cours --}}
                @if($activeOffers->count() > 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">🔥 Offres en cours</h2>
                        <div class="space-y-3">
                            @foreach($activeOffers as $offer)
                                <a href="{{ route('vehicles.show', $offer->vehicle?->slug ?? '#') }}" class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition group">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 bg-red-600 text-white text-xs font-bold rounded mb-1">{{ $offer->badge_text }}</span>
                                        <p class="text-gray-900 font-semibold">{{ $offer->vehicle?->full_name ?? $offer->title }}</p>
                                        @if($offer->description)
                                            <p class="text-gray-500 text-sm">{{ $offer->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black text-red-600">
                                            -{{ $offer->discount_type === 'percentage' ? $offer->discount_value . '%' : number_format($offer->discount_value, 0, ',', ' ') . ' DA' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Véhicules --}}
                <div id="vehicules">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Véhicules disponibles
                        <span class="text-gray-400 font-normal text-lg">({{ $vehicles->total() }})</span>
                    </h2>

                    @if($vehicles->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($vehicles as $vehicle)
                                @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                            @endforeach
                        </div>
                        <div class="mt-10">{{ $vehicles->links() }}</div>
                    @else
                        <div class="text-center py-20">
                            <p class="text-gray-500">Aucun véhicule disponible pour le moment.</p>
                        </div>
                    @endif
                </div>

                {{-- Avis --}}
                <div id="avis">
                    @include('front.components.reviews-section', ['loueur' => $loueur])
                </div>
            </div>

            {{-- Colonne droite (1/3) sidebar --}}
            <div class="space-y-6">

                {{-- Card conditions --}}
                @if(count($conditions) > 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-20">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Conditions de location</h3>
                        <div class="space-y-3">
                            @foreach($conditions as $condition)
                                <div class="flex items-start gap-3">
                                    <span class="text-lg flex-shrink-0">{{ $condition['icon'] ?? 'ℹ️' }}</span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $condition['title'] }}</p>
                                        @if(!empty($condition['description']))
                                            <p class="text-xs text-gray-500">{{ $condition['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Card moyens de paiement --}}
                @if($loueur->payment_methods && count($loueur->payment_methods) > 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Moyens de paiement</h3>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $methodLabels = [
                                    'cash' => 'Espèces', 'cib' => 'CIB', 'dahabia' => 'Dahabia',
                                    'baridimob' => 'BaridiMob', 'paypal' => 'PayPal',
                                    'bank_transfer' => 'Virement', 'wise' => 'En ligne',
                                ];
                            @endphp
                            @foreach($loueur->payment_methods as $method)
                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg font-medium">
                                    {{ $methodLabels[$method] ?? ucfirst($method) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA Réserver --}}
                <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
                    <h3 class="text-lg font-bold text-green-900 mb-3">Réserver un véhicule</h3>
                    <p class="text-green-800 text-sm mb-4">Choisissez un véhicule ci-dessous et réservez directement en ligne via ResaDZ.</p>
                    <a href="#vehicules" class="flex items-center justify-center gap-2 w-full py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        Voir les véhicules
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
