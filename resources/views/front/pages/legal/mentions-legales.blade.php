@extends('front.layouts.app')

@section('title', 'Mentions Légales - ' . $companyName)
@section('meta_description', 'Mentions légales et informations juridiques de ' . $companyName . ', marketplace de location de véhicules en Algérie.')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Mentions Légales</h1>
            <p class="text-gray-300">Informations juridiques et légales</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Éditeur du site</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Le site <strong>{{ $companyName }}</strong> est édité par :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Raison sociale :</strong> {{ $companyName }}</li>
                        @if($companyAddress)
                        <li><strong>Siège social :</strong> {{ $companyAddress }}</li>
                        @endif
                        <li><strong>Email :</strong> <a href="mailto:{{ $companyEmail }}" class="text-green-600 hover:underline">{{ $companyEmail }}</a></li>
                        @if($companyPhone)
                        <li><strong>Téléphone :</strong> {{ $companyPhone }}</li>
                        @endif
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Directeur de la publication</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Le directeur de la publication est le représentant légal de {{ $companyName }}.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Hébergement</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Le site est hébergé par :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Hébergeur :</strong> À définir selon votre hébergeur</li>
                        <li><strong>Adresse :</strong> À compléter</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Propriété intellectuelle</h2>
                <div class="prose prose-gray max-w-none">
                    <p>L'ensemble des éléments constituant ce site (textes, graphismes, logiciels, photographies, images, vidéos, sons, plans, logos, marques, créations et œuvres protégeables diverses, bases de données, etc.) ainsi que le site lui-même, relèvent des législations algériennes et internationales sur le droit d'auteur et la propriété intellectuelle.</p>
                    <p class="mt-4">Ces éléments sont la propriété exclusive de {{ $companyName }}. La reproduction ou représentation, intégrale ou partielle, des pages, des données et de tout autre élément constitutif au site, par quelque procédé ou support que ce soit, est interdite et constitue sans autorisation de l'éditeur une contrefaçon.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Nature du service</h2>
                <div class="prose prose-gray max-w-none">
                    <p>{{ $companyName }} est une plateforme de mise en relation entre des loueurs professionnels de véhicules et des particuliers ou professionnels souhaitant louer un véhicule.</p>
                    <p class="mt-4">{{ $companyName }} n'est pas partie aux contrats de location conclus entre les loueurs et les locataires. {{ $companyName }} agit uniquement en qualité d'intermédiaire et de facilitateur de mise en relation.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Responsabilité</h2>
                <div class="prose prose-gray max-w-none">
                    <p>{{ $companyName }} s'efforce d'assurer au mieux de ses possibilités l'exactitude et la mise à jour des informations diffusées sur ce site. Toutefois, {{ $companyName }} ne peut garantir l'exactitude, la précision ou l'exhaustivité des informations mises à disposition sur ce site.</p>
                    <p class="mt-4">En conséquence, {{ $companyName }} décline toute responsabilité :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Pour toute imprécision, inexactitude ou omission portant sur des informations disponibles sur le site</li>
                        <li>Pour tous dommages résultant d'une intrusion frauduleuse d'un tiers ayant entraîné une modification des informations mises à la disposition sur le site</li>
                        <li>Et plus généralement de tous dommages, directs ou indirects, qu'elles qu'en soient les causes, origines, nature ou conséquences</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Liens hypertextes</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Le site peut contenir des liens hypertextes vers d'autres sites. {{ $companyName }} n'exerce aucun contrôle sur ces sites et décline toute responsabilité quant à leur contenu ou aux dommages qui pourraient résulter de leur utilisation.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Droit applicable</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Les présentes mentions légales sont soumises au droit algérien. En cas de litige, les tribunaux algériens seront seuls compétents.</p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Contact</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Pour toute question concernant ces mentions légales, vous pouvez nous contacter :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Email :</strong> <a href="mailto:{{ $companyEmail }}" class="text-green-600 hover:underline">{{ $companyEmail }}</a></li>
                        @if($companyPhone)
                        <li><strong>Téléphone :</strong> {{ $companyPhone }}</li>
                        @endif
                    </ul>
                </div>
            </section>

        </div>

        <p class="text-center text-gray-500 text-sm mt-8">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
    </div>
</div>
@endsection
