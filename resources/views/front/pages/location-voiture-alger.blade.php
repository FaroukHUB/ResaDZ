@extends('front.layouts.app')

@section('title', 'Location voiture Alger — Loueurs vérifiés dès 4 500 DA/jour | ResaDZ')
@section('meta_description', 'Louez une voiture à Alger auprès de loueurs vérifiés — tarifs réels 2026, livraison aéroport. Réservation en ligne sur ResaDZ.')
@section('canonical', url('/location-voiture-alger'))

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Location voiture Alger",
    "description": "Louez une voiture à Alger auprès de loueurs vérifiés — tarifs réels 2026, livraison aéroport.",
    "url": "{{ url('/location-voiture-alger') }}",
    "publisher": {
        "@@type": "Organization",
        "name": "ResaDZ",
        "url": "{{ url('/') }}"
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Accueil", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Véhicules", "item": "{{ route('vehicles.index') }}" },
        { "@@type": "ListItem", "position": 3, "name": "Location voiture Alger", "item": "{{ url('/location-voiture-alger') }}" }
    ]
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
            <span class="text-white">Location voiture Alger</span>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Location voiture <span style="color: #FF6B2C;">Alger</span> — trouver un loueur de confiance
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-3xl">
            Comparez les loueurs vérifiés à Alger. Prix transparents, contrat automatique, livraison aéroport disponible.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya=alger" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                Voir les voitures disponibles à Alger
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
                Alger est une ville qui ne s'improvise pas. Entre le centre, Bab Ezzouar, Hussein Dey, Dar El Beïda et l'aéroport Houari Boumédiène, les distances sont courtes sur la carte mais longues dans la réalité sans voiture. Que vous soyez résident, en déplacement professionnel, ou Algérien rentré de France pour quelques semaines — louer une voiture à Alger reste la solution la plus pratique et souvent la plus économique.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                ResaDZ est la première plateforme algérienne qui vous connecte directement avec des loueurs vérifiés dans la wilaya d'Alger. Pas d'agence, pas d'intermédiaire opaque — vous voyez le véhicule, le loueur, le prix, et vous réservez en quelques minutes.
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Prix location voiture Alger en 2026 — les vrais tarifs du marché</h2>
        </div>

        <p class="text-gray-700 text-lg leading-relaxed mb-8">
            Le marché algérois de la location via ResaDZ tourne autour de ces fourchettes selon le type de véhicule :
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
                        <td class="px-6 py-4 text-gray-600">Symbol, Clio, Uno</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">4 500 – 6 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Berline</td>
                        <td class="px-6 py-4 text-gray-600">Logan, Sandero, i20</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">6 000 – 7 500 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">SUV</td>
                        <td class="px-6 py-4 text-gray-600">Duster, Stepway</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">8 500 – 10 000 DA</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Van 7 places</td>
                        <td class="px-6 py-4 text-gray-600">Espace, Expert</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">12 000 – 18 000 DA</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-gray-600 text-sm mt-4 italic">
            Ces tarifs sont ceux du marché local des loueurs vérifiés, significativement inférieurs aux agences internationales. En juillet-août, comptez 20 à 30% de plus — réservez à l'avance.
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Où récupérer votre voiture à Alger</h2>
            <p class="mt-2 text-gray-500">La wilaya d'Alger est vaste. Voici les zones où la logistique est la plus simple.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Aéroport --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Aéroport Houari Boumédiène</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    La majorité des loueurs sérieux proposent la livraison directement au terminal. Si vous arrivez de France ou d'Europe, c'est la formule la plus pratique — votre voiture vous attend à la sortie.
                </p>
            </div>

            {{-- Bab Ezzouar --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Bab Ezzouar & Oued Smar</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Zone centrale entre l'aéroport et le centre-ville. Idéale pour les déplacements professionnels ou administratifs.
                </p>
            </div>

            {{-- Bordj El Kiffan --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Bordj El Kiffan & Bordj El Bahri</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Zone est d'Alger, bien desservie, beaucoup de loueurs actifs. Bon point de départ si vous allez vers Boumerdès ou la côte.
                </p>
            </div>

            {{-- Centre-ville --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Centre-ville (Hussein Dey, Kouba)</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Pratique pour les séjours courts intra-muros. Préférez les remises en matinée pour éviter la circulation dense.
                </p>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Alger centre & Sidi M'Hamed</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Alger centre reste le point de rendez-vous le plus demandé : Grande Poste, Didouche Mourad, place Audin. Le stationnement y est difficile en journée — convenez d'un point précis avec le loueur.
                </p>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Hydra, El Biar & Ben Aknoun</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Les hauteurs d'Alger, prisées pour les déplacements professionnels et les séjours en résidence. Accès rapide à l'autoroute et aux ambassades.
                </p>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Chéraga, Ouled Fayet & Dely Ibrahim</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Ouest algérois en pleine expansion, proche du centre d'affaires de Bab Ezzouar via la rocade. Nombreux loueurs disponibles sur cette zone.
                </p>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Dar El Beïda & Rouïba</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    À deux pas de l'aéroport et de la zone industrielle. Idéal si vous arrivez par avion et repartez vers l'est du pays.
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Documents nécessaires pour louer à Alger</h2>
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Louer en toute confiance à Alger — avantages et précautions</h2>
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

{{-- Section ciblant la variante "location de voiture a Alger" --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight mb-8">Location de voiture à Alger : réserver en ligne, sans passer par une agence</h2>
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                La location de voiture à Alger a longtemps voulu dire faire le tour des agences, comparer des prix affichés nulle part et repartir sans garantie. Sur ResaDZ, tout se passe en ligne : vous voyez le véhicule, son prix réel à la journée, les avis laissés par les clients précédents, et vous réservez vos dates en quelques minutes.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Chaque loueur de la plateforme est vérifié — identité, documents du véhicule, assurance — et chaque réservation donne lieu à un contrat. Le montant de la caution, le kilométrage inclus et les conditions d'annulation sont affichés sur l'annonce avant que vous ne payiez quoi que ce soit.
            </p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">Trouver une location de voiture pas chère à Alger</h3>
            <p class="text-gray-700 text-lg leading-relaxed">
                Le prix dépend surtout de trois facteurs : la catégorie du véhicule, la durée et la saison. Une citadine sur une semaine revient nettement moins cher qu'un SUV sur trois jours, et les tarifs baissent mécaniquement au-delà de dix jours de location. Pour les périodes tendues — juillet, août, les fêtes — réserver deux à trois semaines à l'avance fait souvent la différence entre un tarif normal et une majoration de 30 %.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Le comparateur affiche l'ensemble des véhicules disponibles à Alger sur vos dates, du moins cher au plus cher, sans frais de dossier ni commission ajoutée au moment de payer.
            </p>

            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">Combien de temps pour obtenir sa voiture ?</h3>
            <p class="text-gray-700 text-lg leading-relaxed">
                La demande part immédiatement au loueur, qui répond en général dans l'heure. Une fois la réservation confirmée, la remise du véhicule se fait au point convenu — votre quartier, l'aéroport ou votre hôtel — avec l'état des lieux et la signature du contrat sur place, en un quart d'heure environ.
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Questions fréquentes — location voiture Alger</h2>
        </div>

        <div class="space-y-4" x-data="{ open: null }">
            {{-- Q1 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Peut-on louer une voiture à Alger sans se déplacer ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 1 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui. La plupart des loueurs sur ResaDZ proposent la livraison à domicile ou à l'aéroport. Indiquez votre adresse lors de la réservation.</p>
                </div>
            </div>

            {{-- Q2 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Quelle est la durée minimale de location à Alger ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 2 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">La plupart des loueurs acceptent des locations à partir de 24 heures. Certains proposent des demi-journées selon disponibilité.</p>
                </div>
            </div>

            {{-- Q3 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Les prix augmentent-ils en été ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 3 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui, entre juillet et août les tarifs montent de 20 à 30% sur toute la région algéroise. Réservez au moins 2 semaines à l'avance pendant cette période.</p>
                </div>
            </div>

            {{-- Q4 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Un Algérien résidant en France peut-il louer sur ResaDZ ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 4 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 4" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Absolument. Votre permis français est accepté. Beaucoup de loueurs proposent la livraison directe à l'aéroport dès votre arrivée.</p>
                </div>
            </div>

            {{-- Q5 --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button x-on:click="open = open === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Comment éviter les arnaques ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 5 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 5" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Passez uniquement par des plateformes avec contrat et traçabilité. Vérifiez toujours la carte grise et l'assurance avant de partir.</p>
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
                    "name": "Peut-on louer une voiture à Alger sans se déplacer ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Oui. La plupart des loueurs sur ResaDZ proposent la livraison à domicile ou à l'aéroport. Indiquez votre adresse lors de la réservation."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Quelle est la durée minimale de location à Alger ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "La plupart des loueurs acceptent des locations à partir de 24 heures. Certains proposent des demi-journées selon disponibilité."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Les prix augmentent-ils en été ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Oui, entre juillet et août les tarifs montent de 20 à 30% sur toute la région algéroise. Réservez au moins 2 semaines à l'avance pendant cette période."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Un Algérien résidant en France peut-il louer sur ResaDZ ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Absolument. Votre permis français est accepté. Beaucoup de loueurs proposent la livraison directe à l'aéroport dès votre arrivée."
                    }
                },
                {
                    "@@type": "Question",
                    "name": "Comment éviter les arnaques ?",
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": "Passez uniquement par des plateformes avec contrat et traçabilité. Vérifiez toujours la carte grise et l'assurance avant de partir."
                    }
                }
            ]
        }
        </script>
    </div>
</section>

{{-- Maillage interne : villes proches --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-6">Location de voiture autour d'Alger</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/prix-location-voiture-algerie" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Prix location voiture Algérie</a>
            <a href="/location-voiture-sans-caution" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location sans caution</a>
            <a href="/location-voiture-aeroport-alger" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location aéroport Alger</a>
            <a href="/location-voiture-blida" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Blida</a>
            <a href="/location-voiture-boumerdes" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Boumerdès</a>
            <a href="/location-voiture-tipaza" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">Location voiture Tipaza</a>
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-full px-5 py-2.5 hover:bg-gray-700 transition text-sm font-semibold">Tous les véhicules</a>
        </div>
    </div>
</section>

{{-- CTA Final --}}
<section class="bg-gradient-to-r from-green-900 via-green-800 to-green-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight">Trouvez votre voiture à Alger sur ResaDZ</h2>
        <p class="mt-4 text-lg text-green-100 max-w-2xl mx-auto">
            Des loueurs vérifiés, des prix transparents, un contrat automatique à chaque réservation. Plus besoin de chercher sur Facebook ou d'appeler des inconnus.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya=alger" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-900 font-bold rounded-lg hover:bg-gray-100 transition text-lg shadow-xl">
                Voir les voitures disponibles à Alger
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
