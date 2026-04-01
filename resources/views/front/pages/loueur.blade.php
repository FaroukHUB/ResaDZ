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
    </div>

    {{-- Section profil --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- Logo + Nom + Location --}}
            <div class="flex items-start gap-4 md:gap-6 -mt-16 md:-mt-20 mb-6">
                @if($loueur->logo)
                    <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-2xl object-cover shadow-xl border-4 border-white bg-white flex-shrink-0">
                @else
                    <div class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-green-600 to-green-700 rounded-2xl flex items-center justify-center shadow-xl border-4 border-white flex-shrink-0">
                        <span class="text-white font-black text-xl md:text-2xl">{{ strtoupper(substr($loueur->company_name, 0, 2)) }}</span>
                    </div>
                @endif

                <div class="pt-8 md:pt-10">
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900">{{ $loueur->company_name }}</h1>
                    <p class="text-gray-500 text-sm mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $loueur->city ?? '' }} {{ $loueur->wilaya ? '- ' . $loueur->wilaya : '' }}
                    </p>
                </div>
            </div>

            {{-- Infos rapides --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-4 text-sm text-gray-500">
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

            {{-- Réseaux sociaux --}}
            <div class="flex flex-wrap items-center gap-2">
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
                @if($loueur->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $loueur->whatsapp) }}" target="_blank" rel="noopener" class="w-9 h-9 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center transition" title="WhatsApp">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                @endif
                @if($loueur->phone)
                    <a href="tel:{{ $loueur->phone }}" class="w-9 h-9 bg-gray-700 hover:bg-gray-600 text-white rounded-full flex items-center justify-center transition" title="Appeler">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </a>
                @endif
            </div>
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
                <div>
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

                {{-- Card contact rapide --}}
                <div class="bg-green-50 rounded-2xl border border-green-200 p-6">
                    <h3 class="text-lg font-bold text-green-900 mb-3">Contacter {{ $loueur->company_name }}</h3>
                    <div class="space-y-3">
                        @if($loueur->whatsapp || $loueur->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $loueur->whatsapp ?: $loueur->phone) }}" target="_blank"
                               class="flex items-center justify-center gap-2 w-full py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                WhatsApp
                            </a>
                        @endif
                        @if($loueur->phone)
                            <a href="tel:{{ $loueur->phone }}"
                               class="flex items-center justify-center gap-2 w-full py-3 bg-white text-gray-900 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $loueur->phone }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
