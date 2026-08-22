@extends('front.layouts.app')

@php
$faqs = [
    ['q' => 'Peut-on louer une voiture sans caution en Algérie ?', 'a' => 'Oui, certains loueurs n\'exigent aucune caution, en particulier sur les citadines et pour les clients qui présentent un dossier complet. Ce n\'est cependant pas la règle générale : la caution est fixée librement par chaque loueur, et son montant est affiché sur l\'annonce avant toute réservation.'],
    ['q' => 'À quoi sert la caution ?', 'a' => 'Elle couvre le loueur en cas de dommage, d\'amende ou de retard de restitution. Ce n\'est pas un paiement : la somme vous est intégralement rendue à la fin de la location si le véhicule est restitué dans l\'état où vous l\'avez reçu.'],
    ['q' => 'Comment savoir si un véhicule demande une caution avant de réserver ?', 'a' => 'Le montant de la caution figure sur la fiche du véhicule et dans le comparateur, avant l\'étape de paiement. Vous connaissez donc la somme exacte au moment de choisir, sans mauvaise surprise à la remise des clés.'],
    ['q' => 'Combien coûte une caution en moyenne ?', 'a' => 'Cela varie fortement selon la valeur du véhicule : de zéro sur certaines citadines à plusieurs dizaines de milliers de dinars sur un SUV récent. Plus le véhicule est cher, plus la caution demandée est élevée.'],
    ['q' => 'La caution est-elle prélevée sur ma carte bancaire ?', 'a' => 'En Algérie, la caution se règle le plus souvent en espèces à la remise du véhicule, ou fait l\'objet d\'un chèque de garantie non encaissé. Le mode retenu est précisé par le loueur au moment de la réservation.'],
    ['q' => 'Que se passe-t-il si je rends la voiture en retard ?', 'a' => 'Le loueur peut retenir une partie de la caution au titre des heures ou journées supplémentaires. Prévenez-le dès que vous savez que vous serez en retard : la plupart s\'arrangent si vous les informez à temps.'],
    ['q' => 'Peut-on louer sans caution à Alger ou à Oran ?', 'a' => 'Oui, des véhicules sans caution ou à caution réduite sont proposés dans les deux wilayas. Comme ailleurs, cela dépend du loueur et du véhicule : consultez les annonces disponibles à vos dates pour voir celles qui n\'en demandent pas.'],
];
@endphp

@section('title', 'Location voiture sans caution en Algérie — comment ça marche | ResaDZ')
@section('meta_description', 'Louer une voiture sans caution en Algérie : dans quels cas c\'est possible, à quoi sert la caution, comment connaître son montant avant de réserver.')
@section('canonical', url('/location-voiture-sans-caution'))

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Location voiture sans caution en Algérie",
    "description": "Comment fonctionne la caution en location de voiture en Algérie et dans quels cas elle n'est pas demandée.",
    "url": "{{ url('/location-voiture-sans-caution') }}",
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
        { "@@type": "ListItem", "position": 3, "name": "Location sans caution", "item": "{{ url('/location-voiture-sans-caution') }}" }
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
            <span class="text-white">Location sans caution</span>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Location de voiture <span style="color: #FF6B2C;">sans caution</span> en Algérie
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-3xl">
            C'est possible, mais ce n'est pas systématique. Voici comment fonctionne la caution en Algérie, dans quels cas elle n'est pas demandée, et comment connaître son montant exact avant de réserver.
        </p>
        <div class="mt-8">
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                Voir les véhicules et leur caution
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Réponse directe --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-green-50 border-l-4 border-green-600 rounded-r-2xl p-6 mb-10">
            <p class="text-gray-800 text-lg leading-relaxed font-medium">
                La réponse courte : oui, on trouve des véhicules sans caution en Algérie, surtout sur les citadines. Mais la caution reste la norme, et son montant est fixé par chaque loueur. Sur ResaDZ, il est affiché sur l'annonce, avant l'étape de paiement — vous savez donc exactement à quoi vous attendre avant de vous engager.
            </p>
        </div>
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                La caution cristallise beaucoup d'inquiétudes, et souvent à raison : c'est le point où les mauvaises expériences se concentrent. Somme réclamée au dernier moment, montant différent de celui annoncé, restitution qui traîne… Ces situations existent, et elles viennent presque toujours du même problème — une caution jamais écrite noir sur blanc avant la remise des clés.
            </p>
        </div>
    </div>
</section>

{{-- Comment ça marche --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight text-center mb-12">Comment fonctionne la caution</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-black">1</div>
                <h3 class="font-bold text-gray-900 mb-2">Affichée avant de réserver</h3>
                <p class="text-gray-600 text-sm">Le montant figure sur la fiche du véhicule et dans le comparateur. Vous le connaissez avant de choisir.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-black">2</div>
                <h3 class="font-bold text-gray-900 mb-2">Remise avec le véhicule</h3>
                <p class="text-gray-600 text-sm">En espèces ou par chèque de garantie, selon ce que le loueur a indiqué. Elle est notée sur le contrat.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-black">3</div>
                <h3 class="font-bold text-gray-900 mb-2">Restituée à la fin</h3>
                <p class="text-gray-600 text-sm">Intégralement, si le véhicule est rendu dans son état initial et dans les délais convenus.</p>
            </div>
        </div>
    </div>
</section>

{{-- Sans caution : dans quels cas --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight mb-8">Dans quels cas la caution n'est pas demandée</h2>
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                <strong>Sur les véhicules de faible valeur.</strong> C'est le cas le plus courant : sur une citadine d'entrée de gamme, le risque financier est limité et certains loueurs préfèrent s'en passer pour attirer plus de clients.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                <strong>Quand le dossier est complet.</strong> Un locataire qui fournit sa pièce d'identité et son permis dès la demande de réservation rassure. Plusieurs loueurs allègent, voire suppriment, la caution dans ce cas.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                <strong>Pour les clients réguliers.</strong> Après une première location qui s'est bien passée, la relation de confiance change la donne — c'est vrai en Algérie comme ailleurs.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                À l'inverse, attendez-vous à une caution significative sur un SUV récent, un véhicule haut de gamme ou un van : plus la valeur du véhicule est élevée, plus le loueur se couvre.
            </p>
        </div>
    </div>
</section>

{{-- Éviter les mauvaises surprises --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight text-center mb-12">Éviter les mauvaises surprises</h2>
        <div class="grid sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-2">Vérifiez le montant avant de payer</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Il est affiché sur l'annonce. Si un loueur annonce un montant différent à la remise des clés, vous avez la trace écrite de ce qui était convenu.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-2">Photographiez le véhicule</h3>
                <p class="text-gray-600 text-sm leading-relaxed">À la prise en charge comme à la restitution, sous tous les angles, avec le compteur. C'est votre meilleure protection en cas de litige sur un dommage préexistant.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-2">Faites l'état des lieux ensemble</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Signalez chaque rayure, impact ou trace existante avant de partir, et faites-les noter sur le contrat. Cinq minutes qui évitent bien des discussions.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-2">Lisez les avis du loueur</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Les clients précédents mentionnent presque toujours la façon dont la caution a été restituée. C'est l'indicateur le plus fiable dont vous disposez.</p>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">FAQ</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Questions fréquentes sur la caution</h2>
        </div>
        <div class="space-y-4">
            @foreach($faqs as $faq)
            <details class="group bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                <summary class="flex items-center justify-between cursor-pointer px-6 py-5 font-bold text-gray-900 hover:bg-gray-100 transition">
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
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-6">Aller plus loin</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/prix-location-voiture-algerie" class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Prix location voiture Algérie</a>
            <a href="/location-voiture-alger" class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Alger</a>
            <a href="/location-voiture-oran" class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Oran</a>
            <a href="/location-voiture-aeroport-alger" class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location aéroport Alger</a>
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-full px-5 py-2.5 hover:bg-gray-700 transition text-sm font-semibold">Tous les véhicules</a>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="bg-gradient-to-br from-green-700 to-green-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight">Caution affichée, contrat écrit, avis vérifiés</h2>
        <p class="mt-4 text-green-100 text-lg max-w-2xl mx-auto">
            Consultez les véhicules disponibles à vos dates : le montant de la caution est indiqué sur chaque annonce, avant toute réservation.
        </p>
        <div class="mt-8">
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-900 font-bold rounded-lg hover:bg-gray-100 transition text-lg shadow-xl">
                Voir les véhicules disponibles
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
