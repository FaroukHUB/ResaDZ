@extends('front.layouts.app')

@php
$faqs = [
    ['q' => 'Combien coûte une location de voiture en Algérie en 2026 ?', 'a' => 'Comptez 4 000 à 6 000 DA par jour pour une citadine, 5 500 à 7 500 DA pour une berline, 8 000 à 10 000 DA pour un SUV et 11 000 à 18 000 DA pour un van 7 places. Alger reste la wilaya la plus chère, les villes de l\'Ouest et des Hauts Plateaux les plus accessibles.'],
    ['q' => 'Le prix affiché est-il le prix final ?', 'a' => 'Oui. Le tarif journalier affiché sur chaque annonce est celui que vous payez, sans frais de dossier ni commission ajoutée au moment de réserver. Seules les options que vous choisissez explicitement — livraison, siège bébé, conducteur supplémentaire — s\'ajoutent, et elles sont chiffrées avant validation.'],
    ['q' => 'Comment payer moins cher sa location ?', 'a' => 'Trois leviers : louer plus longtemps, car les tarifs deviennent dégressifs au-delà de dix jours ; éviter juillet-août où les prix montent de 20 à 30 % ; et réserver deux à trois semaines à l\'avance, quand les véhicules les mieux notés sont encore disponibles.'],
    ['q' => 'Le carburant est-il compris dans le prix ?', 'a' => 'Non. La règle est le plein-plein : vous récupérez le véhicule avec un niveau donné et le rendez au même niveau. Le carburant reste à votre charge, comme dans toutes les locations en Algérie.'],
    ['q' => 'Y a-t-il une différence de prix entre Alger et les autres wilayas ?', 'a' => 'Oui, d\'environ 10 à 15 %. Alger concentre la demande — déplacements professionnels, diaspora, aéroport — ce qui tire les tarifs vers le haut. À Sidi Bel Abbès, Sétif ou Mascara, les mêmes catégories de véhicules se louent sensiblement moins cher.'],
    ['q' => 'La caution est-elle comprise dans le prix ?', 'a' => 'Non, la caution est distincte du prix de location. Son montant est fixé par le loueur et affiché sur chaque annonce. Elle vous est restituée à la fin de la location si le véhicule est rendu dans son état initial.'],
];
@endphp

@section('title', 'Prix location voiture Algérie 2026 — tarifs réels par ville | ResaDZ')
@section('meta_description', 'Combien coûte une location de voiture en Algérie ? Tarifs réels 2026 par catégorie et par wilaya, ce qui est inclus, et comment payer moins cher.')
@section('canonical', url('/prix-location-voiture-algerie'))

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Prix location voiture Algérie 2026",
    "description": "Tarifs réels de la location de voiture en Algérie par catégorie et par wilaya.",
    "url": "{{ url('/prix-location-voiture-algerie') }}",
    "publisher": { "@@type": "Organization", "name": "ResaDZ", "url": "{{ url('/') }}" }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Accueil", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Véhicules", "item": "{{ route('vehicles.index') }}" },
        { "@@type": "ListItem", "position": 3, "name": "Prix location voiture Algérie", "item": "{{ url('/prix-location-voiture-algerie') }}" }
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $faq)
        {
            "@@type": "Question",
            "name": {!! json_encode($faq['q'], JSON_UNESCAPED_UNICODE) !!},
            "acceptedAnswer": { "@@type": "Answer", "text": {!! json_encode($faq['a'], JSON_UNESCAPED_UNICODE) !!} }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endsection

@section('content')

{{-- Hero --}}
<section class="bg-gray-900 py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-white transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('vehicles.index') }}" class="hover:text-white transition">Véhicules</a>
            <span class="mx-2">/</span>
            <span class="text-white">Prix location voiture Algérie</span>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Prix location voiture <span style="color: #FF6B2C;">Algérie</span> — les tarifs réels en 2026
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-3xl">
            Combien coûte réellement une location de voiture en Algérie ? Voici les fourchettes pratiquées par catégorie et par wilaya, ce que le prix comprend, et les leviers pour payer moins cher.
        </p>
        <div class="mt-8">
            <a href="{{ route('vehicles.index') }}?sort=price_asc" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                Voir les véhicules du moins cher au plus cher
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Intro --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                Le prix d'une location de voiture en Algérie dépend de trois choses : la catégorie du véhicule, la durée de la location et la période de l'année. Un même modèle peut passer du simple à près du double entre un mardi de février et un samedi du mois d'août.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Les fourchettes ci-dessous correspondent au marché réel constaté sur ResaDZ, chez des loueurs vérifiés. Elles sont sensiblement inférieures aux tarifs des agences internationales présentes en Algérie, qui appliquent des grilles calquées sur l'Europe.
            </p>
        </div>
    </div>
</section>

{{-- Prix par catégorie --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Par catégorie</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Combien coûte une location par jour en Algérie</h2>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-900">
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Modèles courants</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider text-right">Prix / jour</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Citadine</td>
                        <td class="px-6 py-4 text-gray-600">Symbol, Clio, i10, Picanto</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">4 000 – 6 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Berline</td>
                        <td class="px-6 py-4 text-gray-600">Logan, Sandero, Accent, i20</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">5 500 – 7 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">SUV</td>
                        <td class="px-6 py-4 text-gray-600">Duster, Stepway, Tucson, Creta</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">8 000 – 10 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Van 7 places</td>
                        <td class="px-6 py-4 text-gray-600">Espace, Expert, Caddy, Partner</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">11 000 – 18 000 DA</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-gray-600 text-sm mt-4 italic">
            Fourchettes constatées hors haute saison. En juillet et août, ainsi que pendant les fêtes, comptez 20 à 30 % de plus sur l'ensemble des catégories.
        </p>
    </div>
</section>

{{-- Prix par ville --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight text-center mb-4">Prix moyens par wilaya</h2>
        <p class="text-gray-600 text-center mb-10 max-w-2xl mx-auto">Alger concentre la demande et affiche les tarifs les plus élevés. L'écart avec les wilayas de l'Ouest et des Hauts Plateaux atteint 10 à 15 % sur les mêmes catégories.</p>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-900">
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Wilaya</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider text-right">Citadine</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider text-right">Berline</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider text-right">SUV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-alger" class="hover:text-green-600 transition">Alger</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 500 – 6 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">6 000 – 7 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 500 – 10 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-oran" class="hover:text-green-600 transition">Oran</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 9 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-constantine" class="hover:text-green-600 transition">Constantine</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 9 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-annaba" class="hover:text-green-600 transition">Annaba</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 9 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-blida" class="hover:text-green-600 transition">Blida</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 9 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-setif" class="hover:text-green-600 transition">Sétif</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 10 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-boumerdes" class="hover:text-green-600 transition">Boumerdès</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 10 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900"><a href="/location-voiture-sidi-bel-abbes" class="hover:text-green-600 transition">Sidi Bel Abbès</a></td>
                        <td class="px-6 py-4 text-right text-gray-700">4 000 – 5 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">5 500 – 7 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-700">8 000 – 9 500 DA</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Ce qui est inclus --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight text-center mb-12">Ce que le prix comprend — et ce qu'il ne comprend pas</h2>
        <div class="grid sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-green-200">
                <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-sm">✓</span>
                    Compris dans le tarif
                </h3>
                <ul class="space-y-2 text-gray-600 text-sm">
                    <li>L'assurance du véhicule</li>
                    <li>Le contrat de location</li>
                    <li>Le kilométrage indiqué sur l'annonce</li>
                    <li>La remise et la restitution au point convenu</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-200">
                <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-gray-400 text-white flex items-center justify-center text-sm">+</span>
                    En supplément éventuel
                </h3>
                <ul class="space-y-2 text-gray-600 text-sm">
                    <li>Le carburant (règle du plein-plein)</li>
                    <li>La caution, restituée en fin de location</li>
                    <li>La livraison hors zone (aéroport, autre wilaya)</li>
                    <li>Les options : siège bébé, GPS, conducteur additionnel</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Payer moins cher --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight mb-8">Comment louer une voiture pas chère en Algérie</h2>
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                <strong>Allongez la durée.</strong> C'est le levier le plus efficace : au-delà de dix jours, les loueurs appliquent des tarifs dégressifs. Une location de deux semaines revient souvent moins cher, au total, que deux locations d'une semaine espacées.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                <strong>Évitez la haute saison si vous le pouvez.</strong> Juillet, août et les périodes de fêtes concentrent la demande, notamment sur le littoral et à Alger. En dehors de ces pics, les mêmes véhicules se louent 20 à 30 % moins cher.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                <strong>Réservez à l'avance.</strong> Contrairement à l'hôtellerie, les prix ne baissent pas à la dernière minute en location de voiture : ce sont les véhicules les moins chers qui partent en premier. Deux à trois semaines d'anticipation suffisent généralement.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                <strong>Prenez la catégorie dont vous avez réellement besoin.</strong> Une citadine suffit pour la ville et consomme moins. Le SUV ne se justifie que pour les longues distances, les routes de montagne ou les trajets vers le Sud.
            </p>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">FAQ</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Questions fréquentes sur les prix</h2>
        </div>
        <div class="space-y-4">
            @foreach($faqs as $faq)
            <details class="group bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <summary class="flex items-center justify-between cursor-pointer px-6 py-5 font-bold text-gray-900 hover:bg-gray-50 transition">
                    {{ $faq['q'] }}
                    <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="px-6 pb-5 text-gray-600 leading-relaxed">{{ $faq['a'] }}</div>
            </details>
            @endforeach
        </div>
    </div>
</section>

{{-- Maillage interne --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-6">Aller plus loin</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/location-voiture-sans-caution" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location sans caution</a>
            <a href="/location-voiture-alger" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Alger</a>
            <a href="/location-voiture-oran" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Oran</a>
            <a href="/location-voiture-aeroport-alger" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location aéroport Alger</a>
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-full px-5 py-2.5 hover:bg-gray-700 transition text-sm font-semibold">Tous les véhicules</a>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="bg-gradient-to-br from-green-700 to-green-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight">Comparez les prix réels, wilaya par wilaya</h2>
        <p class="mt-4 text-green-100 text-lg max-w-2xl mx-auto">
            Tous les véhicules disponibles, triés du moins cher au plus cher, avec le prix final affiché avant réservation.
        </p>
        <div class="mt-8">
            <a href="{{ route('vehicles.index') }}?sort=price_asc" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-900 font-bold rounded-lg hover:bg-gray-100 transition text-lg shadow-xl">
                Voir les tarifs disponibles
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
