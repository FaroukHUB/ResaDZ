@extends('front.layouts.app')

@section('title', 'Location voiture Constantine — Loueurs vérifiés dès 4 000 DA/jour | ResaDZ')
@section('meta_description', "Louez une voiture à Constantine entre particuliers — tarifs réels 2026, loueurs vérifiés. Réservation en ligne sur ResaDZ.")
@section('canonical', url('/location-voiture-constantine'))

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Location voiture Constantine",
    "description": "Louez une voiture à Constantine entre particuliers — tarifs réels 2026, loueurs vérifiés. Réservation en ligne sur ResaDZ.",
    "url": "{{ url('/location-voiture-constantine') }}",
    "publisher": {
        "@@type": "Organization",
        "name": "ResaDZ",
        "url": "{{ url('/') }}"
    }
}
</script>
@endsection

@section('content')

{{-- Hero Section --}}
<section class="bg-gray-900 py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-white transition">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('vehicles.index') }}" class="hover:text-white transition">Véhicules</a>
            <span class="mx-2">/</span>
            <span class="text-white">Location voiture Constantine</span>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Location voiture <span style="color: #FF6B2C;">Constantine</span> — trouver un loueur de confiance
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-3xl">
            Comparez les loueurs particuliers vérifiés à Constantine. Prix transparents, contrat automatique, remise en main propre disponible.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya=constantine" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                Voir les voitures disponibles à Constantine
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Introduction --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                Constantine, la ville des ponts suspendus, est la troisième plus grande ville d'Algérie et le pôle économique de l'Est. Perchée sur un rocher traversé par les gorges du Rhumel, la ville a un relief unique qui rend la voiture indispensable. Que vous visitiez le pont de Sidi M'Cid, que vous soyez en déplacement professionnel ou Constantinois de retour — ResaDZ vous connecte avec des loueurs particuliers vérifiés dans la wilaya de Constantine.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                ResaDZ est la première plateforme algérienne qui vous connecte directement avec des loueurs particuliers vérifiés dans la wilaya de Constantine. Pas d'agence, pas d'intermédiaire opaque — vous voyez le véhicule, le loueur, le prix, et vous réservez en quelques minutes.
            </p>
        </div>
    </div>
</section>

{{-- Prix réels --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Tarifs 2026</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Prix location voiture Constantine en 2026</h2>
        </div>

        <p class="text-gray-700 text-lg leading-relaxed mb-8">
            Le marché constantinois de la location entre particuliers propose des tarifs généralement inférieurs à ceux d'Alger :
        </p>

        {{-- Tableau des prix --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-900">
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Modèles courants</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider text-right">Prix / jour</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Citadine</td>
                        <td class="px-6 py-4 text-gray-600">Symbol, Clio, i10</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">4 000 – 5 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Berline</td>
                        <td class="px-6 py-4 text-gray-600">Logan, Sandero, Accent</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">5 500 – 7 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">SUV</td>
                        <td class="px-6 py-4 text-gray-600">Duster, Tucson, Creta</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">7 500 – 9 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Van 7 places</td>
                        <td class="px-6 py-4 text-gray-600">Espace, Partner</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">10 000 – 15 000 DA</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-gray-600 text-sm mt-4 italic">
            Ces tarifs sont ceux du marché local entre particuliers, généralement inférieurs à ceux pratiqués à Alger. En été, comptez 15 à 25% de plus — réservez à l'avance.
        </p>

        {{-- Tip --}}
        <div class="mt-8 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-5">
            <div class="flex items-start gap-3">
                <span class="text-xl flex-shrink-0">💡</span>
                <p class="text-amber-900 text-sm font-medium">
                    <strong>Astuce :</strong> une location de 7 jours ou plus est souvent négociable directement avec le loueur. N'hésitez pas à le contacter avant de réserver.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Zones pratiques --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Zones</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Où récupérer votre voiture à Constantine</h2>
            <p class="mt-2 text-gray-500">La wilaya de Constantine a une géographie particulière. Voici les zones où la logistique est la plus simple.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Aéroport --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Aéroport Mohamed Boudiaf</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    L'aéroport international de Constantine est situé à Aïn El Bey, à 10 km au sud de la ville. Plusieurs loueurs proposent la livraison directement au terminal.
                </p>
            </div>

            {{-- Centre-ville --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Centre-ville (Place des Martyrs, Didouche Mourad)</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Le coeur historique de Constantine. Idéal pour un séjour touristique ou un déplacement professionnel. Attention au stationnement dans les rues étroites du vieux centre.
                </p>
            </div>

            {{-- Ali Mendjeli --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Ali Mendjeli (nouvelle ville)</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    La nouvelle ville d'Ali Mendjeli, avec sa zone commerciale et son université, est un point de départ pratique. Routes larges et stationnement facile.
                </p>
            </div>

            {{-- El Khroub --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">El Khroub (sud de Constantine)</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Située au sud de Constantine, El Khroub offre un accès direct à l'autoroute Est-Ouest. Idéal si vous partez vers Sétif, Batna ou Annaba.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Documents --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Documents</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Documents nécessaires pour louer à Constantine</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Côté locataire --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    Côté locataire
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Permis de conduire valide (algérien ou international)
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Carte nationale d'identité ou passeport
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Numéro de téléphone algérien joignable
                    </li>
                </ul>
            </div>

            {{-- Côté véhicule --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    Côté véhicule — exigez toujours
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Carte grise au nom du propriétaire
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Attestation d'assurance valide
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Vignette technique à jour
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        État des lieux écrit avec photos avant le départ
                    </li>
                </ul>
            </div>
        </div>

        {{-- Alerte --}}
        <div class="mt-8 bg-red-50 border-l-4 border-red-400 rounded-r-xl p-5">
            <div class="flex items-start gap-3">
                <span class="text-xl flex-shrink-0">⚠️</span>
                <p class="text-red-900 text-sm font-medium">
                    <strong>Conseil important :</strong> prenez vous-même des photos du véhicule sous tous les angles avant de partir et envoyez-les au loueur par message. Vous avez ainsi une trace horodatée incontestable.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Particulier vs Agence --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Avantages</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Louer entre particuliers à Constantine — avantages et précautions</h2>
        </div>

        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                Louer directement à un particulier présente des avantages réels — prix plus bas, flexibilité sur les horaires, livraison à domicile souvent possible. Le risque sans cadre structuré : pas de contrat clair, assurance parfois floue, aucun recours en cas de problème.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                C'est pourquoi ResaDZ impose un cadre à chaque location : contrat généré automatiquement, loueurs vérifiés, messagerie intégrée pour garder une trace de chaque échange.
            </p>
        </div>

        {{-- Avantages grid --}}
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-2xl p-5 border border-green-200 text-center">
                <div class="text-3xl mb-2">📝</div>
                <p class="font-bold text-green-900 text-sm">Contrat automatique</p>
                <p class="text-green-800 text-xs mt-1">Généré à chaque réservation</p>
            </div>
            <div class="bg-green-50 rounded-2xl p-5 border border-green-200 text-center">
                <div class="text-3xl mb-2">✅</div>
                <p class="font-bold text-green-900 text-sm">Loueurs vérifiés</p>
                <p class="text-green-800 text-xs mt-1">Profils contrôlés par ResaDZ</p>
            </div>
            <div class="bg-green-50 rounded-2xl p-5 border border-green-200 text-center">
                <div class="text-3xl mb-2">💬</div>
                <p class="font-bold text-green-900 text-sm">Messagerie intégrée</p>
                <p class="text-green-800 text-xs mt-1">Échanges tracés et sécurisés</p>
            </div>
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Questions fréquentes — location voiture Constantine</h2>
        </div>

        <div class="space-y-4" x-data="{ open: null }">
            {{-- Q1 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Peut-on se faire livrer à l'aéroport de Constantine ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 1 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui, des loueurs proposent la livraison directement au terminal de l'aéroport Mohamed Boudiaf. Indiquez votre heure d'arrivée lors de la réservation.</p>
                </div>
            </div>

            {{-- Q2 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Peut-on louer pour visiter Djemila ou Timgad ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 2 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui, c'est idéal. Constantine est un excellent point de départ pour visiter les sites archéologiques de l'Est algérien, comme Djemila (wilaya de Jijel) et Timgad (wilaya de Batna), accessibles en 1h30 à 2h de route.</p>
                </div>
            </div>

            {{-- Q3 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Les routes sont-elles praticables en hiver ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 3 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Constantine peut avoir de la neige en hiver, surtout sur les hauteurs. Un SUV est conseillé entre décembre et février si vous prévoyez des trajets hors de la ville.</p>
                </div>
            </div>

            {{-- Q4 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Quelle est la durée minimale de location ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 4 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 4" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">À partir de 24 heures pour la plupart des loueurs. Certains proposent des demi-journées selon disponibilité.</p>
                </div>
            </div>

            {{-- Q5 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Un permis étranger est-il accepté ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 5 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 5" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui, les permis français, belge et canadien sont acceptés par les loueurs. Munissez-vous également d'une pièce d'identité valide.</p>
                </div>
            </div>
        </div>

        {{-- FAQ Schema --}}
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "FAQPage",
            "mainEntity": [
                {
                    "@@type": "Question",
                    "name": "Peut-on se faire livrer à l'aéroport de Constantine ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Oui, des loueurs proposent la livraison directement au terminal de l'aéroport Mohamed Boudiaf. Indiquez votre heure d'arrivée lors de la réservation."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Peut-on louer pour visiter Djemila ou Timgad ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Oui, c'est idéal. Constantine est un excellent point de départ pour visiter les sites archéologiques de l'Est algérien, comme Djemila et Timgad, accessibles en 1h30 à 2h de route."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Les routes sont-elles praticables en hiver ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Constantine peut avoir de la neige en hiver, surtout sur les hauteurs. Un SUV est conseillé entre décembre et février si vous prévoyez des trajets hors de la ville."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Quelle est la durée minimale de location ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "À partir de 24 heures pour la plupart des loueurs. Certains proposent des demi-journées selon disponibilité."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Un permis étranger est-il accepté ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Oui, les permis français, belge et canadien sont acceptés par les loueurs. Munissez-vous également d'une pièce d'identité valide."
                    }
                }
            ]
        }
        </script>
    </div>
</section>

{{-- CTA Final --}}
<section class="bg-gradient-to-r from-green-900 via-green-800 to-green-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight">Trouvez votre voiture à Constantine sur ResaDZ</h2>
        <p class="mt-4 text-lg text-green-100 max-w-2xl mx-auto">
            Des loueurs vérifiés, des prix transparents, un contrat automatique à chaque réservation. Plus besoin de chercher sur Facebook ou d'appeler des inconnus.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya=constantine" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-900 font-bold rounded-lg hover:bg-gray-100 transition text-lg shadow-xl">
                Voir les voitures disponibles à Constantine
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
