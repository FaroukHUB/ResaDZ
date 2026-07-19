{{--
    Template riche pour les pages SEO aéroport.
    Variables : $city, $slug (slug wilaya), $airportName, $airportCode,
    $metaTitle, $metaDescription, $heroSubtitle, $intro (array),
    $priceRows (array), $priceNote, $deliveryText (array),
    $faqs (array [q, a]), $nearby (array [url => label])
--}}

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('canonical', url('/location-voiture-aeroport-' . $slug))

@section('meta_extra')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Location voiture aéroport {{ $city }}",
    "description": {!! json_encode($metaDescription, JSON_UNESCAPED_UNICODE) !!},
    "url": "{{ url('/location-voiture-aeroport-' . $slug) }}",
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
        { "@@type": "ListItem", "position": 2, "name": "Location voiture {{ $city }}", "item": "{{ url('/location-voiture-' . $slug) }}" },
        { "@@type": "ListItem", "position": 3, "name": "Aéroport {{ $city }}", "item": "{{ url('/location-voiture-aeroport-' . $slug) }}" }
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
            <a href="{{ url('/location-voiture-' . $slug) }}" class="hover:text-white transition">Location voiture {{ $city }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">Aéroport {{ $city }}</span>
        </nav>

        <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Location voiture aéroport <span style="color: #FF6B2C;">{{ $city }}</span> — livraison au terminal
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-3xl">{{ $heroSubtitle }}</p>
        <div class="mt-8">
            <a href="/vehicules?wilaya={{ $slug }}" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                Voir les voitures disponibles
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Introduction --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            @foreach($intro as $paragraph)
                <p class="text-gray-700 text-lg leading-relaxed {{ $loop->first ? '' : 'mt-4' }}">{{ $paragraph }}</p>
            @endforeach
        </div>
    </div>
</section>

{{-- Comment ça marche --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight text-center mb-12">Comment récupérer votre voiture à l'aéroport {{ $airportName }}</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-black">1</div>
                <h3 class="font-bold text-gray-900 mb-2">Réservez en ligne</h3>
                <p class="text-gray-600 text-sm">Choisissez votre véhicule sur ResaDZ, indiquez votre vol et l'heure d'arrivée. Cochez l'option livraison aéroport.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-black">2</div>
                <h3 class="font-bold text-gray-900 mb-2">Le loueur vous attend</h3>
                <p class="text-gray-600 text-sm">À votre atterrissage ({{ $airportCode }}), le loueur vous attend au terminal ou au parking avec le véhicule et le contrat.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-xl font-black">3</div>
                <h3 class="font-bold text-gray-900 mb-2">Prenez la route</h3>
                <p class="text-gray-600 text-sm">État des lieux rapide, signature, remise des clés — vous quittez l'aéroport au volant en quelques minutes.</p>
            </div>
        </div>
    </div>
</section>

{{-- Tarifs --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-3 mb-4">
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
                <span class="text-green-600 text-sm font-semibold uppercase tracking-wider">Tarifs 2026</span>
                <div class="w-8 h-1 bg-gradient-to-r from-green-500 to-green-700 rounded-full"></div>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Tarifs location voiture aéroport {{ $city }}</h2>
        </div>

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
                    @foreach($priceRows as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $row[0] }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $row[1] }}</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #FF6B2C;">{{ $row[2] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-gray-600 text-sm mt-4 italic">{{ $priceNote }}</p>
    </div>
</section>

{{-- Livraison / frais --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight mb-8">Livraison à l'aéroport : comment ça se passe</h2>
        <div class="prose prose-lg max-w-none">
            @foreach($deliveryText as $paragraph)
                <p class="text-gray-700 text-lg leading-relaxed {{ $loop->first ? '' : 'mt-4' }}">{{ $paragraph }}</p>
            @endforeach
        </div>
    </div>
</section>

{{-- Documents --}}
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight text-center mb-12">Documents à préparer avant votre arrivée</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center">
                <div class="text-4xl mb-3">🪪</div>
                <h3 class="font-bold text-gray-900 mb-2">Permis de conduire</h3>
                <p class="text-gray-600 text-sm">Permis algérien ou étranger accompagné du permis international selon le loueur. 2 ans d'ancienneté en général.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center">
                <div class="text-4xl mb-3">🛂</div>
                <h3 class="font-bold text-gray-900 mb-2">Passeport</h3>
                <p class="text-gray-600 text-sm">Pour les résidents à l'étranger, le passeport sert de pièce d'identité pour le contrat de location.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center">
                <div class="text-4xl mb-3">💳</div>
                <h3 class="font-bold text-gray-900 mb-2">Caution</h3>
                <p class="text-gray-600 text-sm">Le montant et le mode (espèces ou empreinte) sont précisés sur l'annonce — préparez-le avant le vol.</p>
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
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight">Questions fréquentes — location aéroport {{ $city }}</h2>
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
        <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-6">Location de voiture ailleurs en Algérie</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($nearby as $url => $label)
                <a href="{{ $url }}" class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 hover:border-green-400 hover:bg-green-50 transition text-sm font-semibold text-gray-700">{{ $label }}</a>
            @endforeach
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-full px-5 py-2.5 hover:bg-gray-700 transition text-sm font-semibold">Tous les véhicules</a>
        </div>
    </div>
</section>

{{-- CTA final --}}
<section class="bg-gradient-to-br from-green-700 to-green-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight">Réservez votre voiture à l'aéroport {{ $airportName }}</h2>
        <p class="mt-4 text-green-100 text-lg max-w-2xl mx-auto">
            Atterrissez tranquille : votre voiture vous attend au terminal. Loueurs vérifiés, contrat automatique, prix affichés.
        </p>
        <div class="mt-8">
            <a href="/vehicules?wilaya={{ $slug }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-green-900 font-bold rounded-lg hover:bg-gray-100 transition text-lg shadow-xl">
                Voir les véhicules disponibles
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
