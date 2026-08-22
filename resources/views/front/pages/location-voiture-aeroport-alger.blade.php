@extends('front.layouts.app')

@section('title', 'Location de voiture aéroport Alger — livraison terminal | ResaDZ')
@section('meta_description', "Louez une voiture à l'aéroport d'Alger Houari Boumédiène — livraison directe au terminal, loueurs vérifiés, réservation en ligne. Dès 4 500 DA.")
@section('canonical', url('/location-voiture-aeroport-alger'))

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Location voiture Aéroport Alger Houari Boumédiène",
    "description": "Louez une voiture à l'aéroport d'Alger — livraison directe au terminal, loueurs vérifiés.",
    "url": "{{ url('/location-voiture-aeroport-alger') }}",
    "publisher": {
        "@@type": "Organization",
        "name": "ResaDZ",
        "url": "{{ url('/') }}"
    }
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
            <a href="{{ url('/location-voiture-alger') }}" class="hover:text-white transition">Location voiture Alger</a>
            <span class="mx-2">/</span>
            <span class="text-white">Aéroport</span>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Location de voiture <span style="color: #FF6B2C;">aéroport Alger</span> Houari Boumédiène — livraison au terminal
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-3xl">
            Votre voiture vous attend à la sortie du terminal. Loueurs vérifiés, livraison directe, contrat automatique.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya=alger&livraison=aeroport" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                Voir les voitures avec livraison aéroport
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
                L'aéroport Houari Boumédiène de Dar El Beïda est le point d'entrée de la majorité des voyageurs qui arrivent en Algérie depuis l'Europe, le Moyen-Orient ou l'Amérique du Nord. Après un vol de 2 à 10 heures, la dernière chose dont vous avez envie c'est de chercher un taxi ou d'attendre un transfert incertain.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Louer une voiture directement à l'aéroport d'Alger vous donne une liberté totale dès la sortie du terminal — que vous alliez directement à Alger centre, à Boumerdès, à Blida ou que vous partiez directement vers l'est ou l'ouest du pays.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Sur ResaDZ, des loueurs basés à Dar El Beïda, Bab Ezzouar et Bordj El Bahri proposent la livraison directe au terminal — votre voiture vous attend, clés en main, dès votre arrivée.
            </p>
        </div>
    </div>
</section>

{{-- Comment ça marche --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Processus</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Comment récupérer votre voiture à l'aéroport d'Alger</h2>
        </div>

        <div class="space-y-6">
            {{-- Étape 1 --}}
            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-black text-lg">1</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm flex-1">
                    <h3 class="font-bold text-gray-900 text-lg">Réservez votre véhicule sur ResaDZ</h3>
                    <p class="text-gray-600 mt-2">Indiquez votre heure d'arrivée et votre numéro de vol lors de la réservation. Le loueur saura exactement quand vous attendre.</p>
                </div>
            </div>

            {{-- Étape 2 --}}
            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-black text-lg">2</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm flex-1">
                    <h3 class="font-bold text-gray-900 text-lg">Le loueur confirme et vous contacte</h3>
                    <p class="text-gray-600 mt-2">Il confirme votre réservation et vous contacte pour coordonner le point de remise des clés — directement au terminal ou au parking.</p>
                </div>
            </div>

            {{-- Étape 3 --}}
            <div class="flex gap-6 items-start">
                <div class="flex-shrink-0 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-black text-lg">3</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm flex-1">
                    <h3 class="font-bold text-gray-900 text-lg">Votre voiture est déjà là</h3>
                    <p class="text-gray-600 mt-2">À votre sortie du terminal, état des lieux, contrat signé, vous partez immédiatement. Pas d'attente, pas de mauvaise surprise.</p>
                </div>
            </div>
        </div>

        {{-- Tip --}}
        <div class="mt-8 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-5">
            <div class="flex items-start gap-3">
                <span class="text-xl flex-shrink-0">💡</span>
                <p class="text-amber-900 text-sm font-medium">
                    <strong>Conseil :</strong> réservez au minimum 48 heures à l'avance, surtout en juillet-août. Les véhicules disponibles à l'aéroport partent vite pendant la saison estivale.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Prix --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Tarifs 2026</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Tarifs location voiture aéroport Alger 2026</h2>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-900">
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider">Prix / jour</th>
                        <th class="px-6 py-4 text-sm font-bold text-white uppercase tracking-wider text-right">Livraison aéroport</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Citadine</td>
                        <td class="px-6 py-4 font-bold" style="color: #FF6B2C;">4 500 – 6 000 DA</td>
                        <td class="px-6 py-4 text-right text-gray-600">Incluse ou faible coût</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Berline</td>
                        <td class="px-6 py-4 font-bold" style="color: #FF6B2C;">6 000 – 7 500 DA</td>
                        <td class="px-6 py-4 text-right text-gray-600">Incluse ou faible coût</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">SUV</td>
                        <td class="px-6 py-4 font-bold" style="color: #FF6B2C;">8 500 – 10 000 DA</td>
                        <td class="px-6 py-4 text-right text-green-600 font-semibold">Incluse</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">Van 7 places</td>
                        <td class="px-6 py-4 font-bold" style="color: #FF6B2C;">12 000 – 18 000 DA</td>
                        <td class="px-6 py-4 text-right text-green-600 font-semibold">Incluse</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-gray-600 text-sm mt-4 italic">
            Certains loueurs offrent la livraison aéroport à partir de 7 jours de location. Vérifiez les conditions de chaque loueur directement sur leur fiche ResaDZ.
        </p>
    </div>
</section>

{{-- Diaspora --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Diaspora</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Vous rentrez depuis la France ou l'Europe ? Ce qu'il faut savoir</h2>
        </div>

        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                Une grande partie des demandes de location à l'aéroport d'Alger vient de la diaspora algérienne établie en France, en Belgique, au Canada ou dans les pays du Golfe. Ces voyageurs ont des besoins spécifiques : arrivée tardive, beaucoup de bagages, famille nombreuse, séjour de 2 à 6 semaines.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Sur ResaDZ, plusieurs loueurs proposent des formules adaptées à ces profils : livraison nocturne, véhicules 7 places, tarifs dégressifs pour les longues durées.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Votre permis de conduire français, belge ou canadien est accepté. Le permis international n'est pas obligatoire mais conseillé pour les résidents hors Algérie.
            </p>
        </div>

        {{-- Info --}}
        <div class="mt-8 bg-indigo-50 border-l-4 border-indigo-400 rounded-r-xl p-5">
            <div class="flex items-start gap-3">
                <span class="text-xl flex-shrink-0">ℹ️</span>
                <p class="text-indigo-900 text-sm font-medium">
                    <strong>Bon à savoir :</strong> si votre passeport algérien est expiré, une disposition exceptionnelle permet aux résidents à l'étranger d'entrer sur le territoire avec un passeport étranger valide. Renseignez-vous auprès du consulat avant de voyager.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Frais de livraison --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Livraison</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Y a-t-il des frais de livraison à l'aéroport ?</h2>
            <p class="mt-2 text-gray-500">Les frais varient selon les loueurs. Sur ResaDZ, trois cas de figure :</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-green-50 rounded-2xl p-6 border border-green-200 text-center">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-bold text-green-900 text-lg">Incluse dans le prix</h3>
                <p class="text-green-800 text-sm mt-2">Certains loueurs incluent la livraison aéroport sans supplément, quelle que soit la durée de location.</p>
            </div>

            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-200 text-center">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-amber-900 text-lg">Offerte dès X jours</h3>
                <p class="text-amber-800 text-sm mt-2">D'autres offrent la livraison aéroport pour toute location de 7 jours ou plus. Badge clairement visible sur la fiche.</p>
            </div>

            <div class="bg-gray-100 rounded-2xl p-6 border border-gray-200 text-center">
                <div class="w-12 h-12 bg-gray-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg">Petit supplément</h3>
                <p class="text-gray-700 text-sm mt-2">Un frais de livraison s'applique selon la zone. Montant toujours affiché avant la réservation — aucune surprise.</p>
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
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Préparation</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Documents à préparer avant votre arrivée</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
                        Permis de conduire valide
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Passeport ou CNI
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Numéro de vol ou heure d'arrivée
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    Côté véhicule à vérifier
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Carte grise
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Attestation d'assurance
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        État des lieux signé avant départ
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Guide pratique (contenu consolide depuis les anciens articles de blog aeroport) --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight mb-8">Louer une voiture à Alger aéroport : le guide complet</h2>
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 text-lg leading-relaxed">
                L'aéroport Houari Boumédiène compte trois aérogares : le terminal 1 pour l'international, le terminal 2 pour le domestique et le terminal 4 pour les vols low-cost. Quel que soit votre point d'arrivée, la remise du véhicule se fait à la sortie des arrivées ou au parking attenant — précisez simplement votre terminal au loueur lors de la réservation.
            </p>
            <p class="text-gray-700 text-lg leading-relaxed mt-4">
                Comptez une quinzaine de minutes entre la sortie de la douane et le départ au volant : le temps de l'état des lieux, de la signature du contrat et de la remise des clés. Pour un vol arrivant de nuit, signalez-le à la réservation — la majorité des loueurs assurent les remises tardives sans supplément, mais mieux vaut l'avoir confirmé avant le décollage.
            </p>
            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">Combien coûte une location de voiture à l'aéroport d'Alger ?</h3>
            <p class="text-gray-700 text-lg leading-relaxed">
                Les tarifs sont identiques à ceux du centre-ville — le tableau ci-dessus s'applique. Seule la livraison à l'aérogare peut faire l'objet d'un supplément, généralement compris entre 1 000 et 2 000 DA, et affiché sur chaque annonce avant la réservation. En haute saison estivale, comptez 20 à 30 % de plus sur le prix journalier, et réservez plusieurs semaines à l'avance : c'est la période où les véhicules partent le plus vite.
            </p>
            <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">Réserver depuis l'étranger avant son vol</h3>
            <p class="text-gray-700 text-lg leading-relaxed">
                La réservation se fait entièrement en ligne, sans avance obligatoire dans la plupart des cas. Communiquez votre numéro de vol : le loueur suit l'horaire réel d'atterrissage et s'adapte en cas de retard. Pour le retour, la restitution à l'aérogare se convient de la même manière — prévoyez trente minutes de marge avant votre enregistrement.
            </p>
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Questions fréquentes — location aéroport Alger</h2>
        </div>

        <div class="space-y-4" x-data="{ open: null }">
            <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                <button x-on:click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Peut-on réserver le jour même ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 1 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">C'est possible mais risqué en haute saison. Mieux vaut réserver au moins 48h à l'avance pour garantir la disponibilité d'un véhicule au terminal.</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                <button x-on:click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">La voiture est-elle disponible la nuit ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 2 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Certains loueurs assurent la livraison 24h/24. Précisez votre heure d'arrivée lors de la réservation et confirmez avec le loueur.</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                <button x-on:click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Que faire si le vol est retardé ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 3 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Prévenez immédiatement le loueur par message sur ResaDZ. La majorité s'adapte aux retards de vol sans frais supplémentaires.</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                <button x-on:click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Peut-on rendre la voiture à l'aéroport ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 4 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 4" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui, la plupart des loueurs acceptent la restitution au terminal au moment de votre départ. À confirmer lors de la réservation.</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                <button x-on:click="open = open === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                    <span class="font-bold text-gray-900">Un permis étranger est-il accepté ?</span>
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === 5 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 5" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600">Oui. Permis français, belge, canadien et international sont acceptés.</p>
                </div>
            </div>
        </div>

        {{-- FAQ Schema --}}
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "FAQPage",
            "mainEntity": [
                {"@@type": "Question", "name": "Peut-on réserver le jour même ?", "acceptedAnswer": {"@@type": "Answer", "text": "C'est possible mais risqué en haute saison. Mieux vaut réserver au moins 48h à l'avance."}},
                {"@@type": "Question", "name": "La voiture est-elle disponible la nuit ?", "acceptedAnswer": {"@@type": "Answer", "text": "Certains loueurs assurent la livraison 24h/24. Précisez votre heure d'arrivée lors de la réservation."}},
                {"@@type": "Question", "name": "Que faire si le vol est retardé ?", "acceptedAnswer": {"@@type": "Answer", "text": "Prévenez le loueur par message sur ResaDZ. La majorité s'adapte aux retards sans frais supplémentaires."}},
                {"@@type": "Question", "name": "Peut-on rendre la voiture à l'aéroport ?", "acceptedAnswer": {"@@type": "Answer", "text": "Oui, la plupart des loueurs acceptent la restitution au terminal. À confirmer lors de la réservation."}},
                {"@@type": "Question", "name": "Un permis étranger est-il accepté ?", "acceptedAnswer": {"@@type": "Answer", "text": "Oui. Permis français, belge, canadien et international sont acceptés."}}
            ]
        }
        </script>
    </div>
</section>

{{-- CTA Final --}}
<section class="bg-gradient-to-r from-green-900 via-green-800 to-green-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight">Réservez votre voiture à l'aéroport d'Alger maintenant</h2>
        <p class="mt-4 text-lg text-green-100 max-w-2xl mx-auto">
            Ne laissez pas le hasard décider de votre transport à l'arrivée. Réservez à l'avance, votre voiture vous attend à la sortie du terminal.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya=alger&livraison=aeroport" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-900 font-bold rounded-lg hover:bg-gray-100 transition text-lg shadow-xl">
                Voir les voitures avec livraison aéroport
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
