@extends('front.layouts.app')

@section('title', 'Conditions Générales d\'Utilisation - ' . $companyName)
@section('meta_description', 'Conditions générales d\'utilisation de ' . $companyName . ', marketplace de location de véhicules en Algérie.')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Conditions Générales d'Utilisation</h1>
            <p class="text-gray-300">Règles d'utilisation de la plateforme</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Objet</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Les présentes Conditions Générales d'Utilisation (CGU) ont pour objet de définir les modalités et conditions d'utilisation des services proposés par {{ $companyName }} (ci-après "la Plateforme"), ainsi que de définir les droits et obligations des parties dans ce cadre.</p>
                    <p class="mt-4">L'utilisation de la Plateforme implique l'acceptation pleine et entière des présentes CGU.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Définitions</h2>
                <div class="prose prose-gray max-w-none">
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Plateforme :</strong> désigne le site internet {{ $companyName }} et l'ensemble de ses services</li>
                        <li><strong>Utilisateur :</strong> désigne toute personne qui accède à la Plateforme</li>
                        <li><strong>Loueur :</strong> désigne tout professionnel de la location de véhicules inscrit sur la Plateforme</li>
                        <li><strong>Locataire :</strong> désigne toute personne effectuant une réservation via la Plateforme</li>
                        <li><strong>Véhicule :</strong> désigne tout véhicule proposé à la location sur la Plateforme</li>
                        <li><strong>Réservation :</strong> désigne la demande de location d'un véhicule effectuée par un Locataire</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Accès à la Plateforme</h2>
                <div class="prose prose-gray max-w-none">
                    <p>L'accès à la Plateforme est gratuit pour les Utilisateurs et Locataires. Les Loueurs peuvent être soumis à des frais de service ou de commission selon les conditions définies dans leur contrat avec {{ $companyName }}.</p>
                    <p class="mt-4">{{ $companyName }} se réserve le droit de suspendre ou de fermer l'accès à la Plateforme à tout moment pour maintenance ou mise à jour, sans préavis ni indemnité.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Inscription des Loueurs</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Pour proposer des véhicules à la location sur la Plateforme, les Loueurs doivent :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Créer un compte en fournissant des informations exactes et à jour</li>
                        <li>Être un professionnel de la location de véhicules dûment enregistré</li>
                        <li>Disposer de tous les documents et autorisations nécessaires à l'exercice de leur activité</li>
                        <li>S'assurer que leurs véhicules sont en bon état et conformes à la réglementation</li>
                        <li>Souscrire les assurances obligatoires pour leurs véhicules</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Réservation</h2>
                <div class="prose prose-gray max-w-none">
                    <p>La réservation d'un véhicule s'effectue selon les étapes suivantes :</p>
                    <ol class="list-decimal pl-6 mt-4 space-y-2">
                        <li>Le Locataire sélectionne un véhicule et les dates de location souhaitées</li>
                        <li>Le Locataire remplit le formulaire de réservation avec ses informations personnelles</li>
                        <li>Le Locataire verse un acompte (si requis par le Loueur)</li>
                        <li>Le Loueur confirme ou refuse la réservation</li>
                        <li>En cas de confirmation, un contrat de location est établi entre le Loueur et le Locataire</li>
                    </ol>
                    <p class="mt-4">{{ $companyName }} n'est pas partie au contrat de location. {{ $companyName }} agit uniquement en qualité d'intermédiaire facilitant la mise en relation.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Prix et paiement</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Les prix des locations sont fixés par les Loueurs et affichés sur la Plateforme. Ils peuvent inclure :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Le prix de base de la location (par jour, semaine ou mois)</li>
                        <li>Les frais de livraison et de retour</li>
                        <li>Les options supplémentaires (GPS, siège bébé, etc.)</li>
                        <li>Les frais de service de la Plateforme</li>
                    </ul>
                    <p class="mt-4">Le montant de la caution est défini par chaque Loueur et communiqué avant la réservation.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Annulation</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Les conditions d'annulation sont définies par chaque Loueur. En cas d'annulation :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Par le Locataire : les conditions de remboursement dépendent de la politique du Loueur</li>
                        <li>Par le Loueur : le Locataire sera intégralement remboursé des sommes versées</li>
                    </ul>
                    <p class="mt-4">{{ $companyName }} n'est pas responsable des conséquences d'une annulation, notamment des frais supplémentaires engagés par le Locataire (transport, hébergement, etc.).</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Obligations du Locataire</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Le Locataire s'engage à :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Fournir des informations exactes lors de la réservation</li>
                        <li>Être titulaire d'un permis de conduire valide</li>
                        <li>Respecter l'âge minimum requis par le Loueur</li>
                        <li>Utiliser le véhicule conformément à sa destination</li>
                        <li>Restituer le véhicule dans l'état où il l'a reçu</li>
                        <li>Signaler immédiatement tout dommage ou incident</li>
                        <li>Respecter le code de la route</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Obligations du Loueur</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Le Loueur s'engage à :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Fournir un véhicule conforme à la description</li>
                        <li>S'assurer que le véhicule est en bon état de fonctionnement</li>
                        <li>Disposer d'une assurance valide couvrant le véhicule</li>
                        <li>Remettre tous les documents nécessaires au Locataire</li>
                        <li>Être disponible pour la remise et la restitution du véhicule</li>
                        <li>Répondre rapidement aux demandes et messages</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Responsabilité de {{ $companyName }}</h2>
                <div class="prose prose-gray max-w-none">
                    <p>{{ $companyName }} agit en tant qu'intermédiaire et n'est pas responsable :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>De l'état des véhicules proposés par les Loueurs</li>
                        <li>Du comportement des Loueurs ou des Locataires</li>
                        <li>Des dommages causés pendant la période de location</li>
                        <li>Des litiges entre Loueurs et Locataires</li>
                    </ul>
                    <p class="mt-4">{{ $companyName }} met tout en œuvre pour vérifier la qualité des Loueurs inscrits sur la Plateforme, mais ne peut garantir leur fiabilité.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Avis et évaluations</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Après chaque location, le Locataire peut laisser un avis sur le Loueur et le véhicule. Les avis doivent :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Être honnêtes et refléter une expérience réelle</li>
                        <li>Ne pas contenir de propos injurieux, diffamatoires ou illicites</li>
                        <li>Respecter la vie privée des personnes</li>
                    </ul>
                    <p class="mt-4">{{ $companyName }} se réserve le droit de modérer ou supprimer les avis ne respectant pas ces règles.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Protection des données personnelles</h2>
                <div class="prose prose-gray max-w-none">
                    <p>{{ $companyName }} collecte et traite les données personnelles conformément à sa <a href="{{ route('legal.confidentialite') }}" class="text-green-600 hover:underline">Politique de Confidentialité</a>.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Modification des CGU</h2>
                <div class="prose prose-gray max-w-none">
                    <p>{{ $companyName }} se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés des modifications par tout moyen approprié. L'utilisation de la Plateforme après modification vaut acceptation des nouvelles CGU.</p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Droit applicable et juridiction</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Les présentes CGU sont soumises au droit algérien. En cas de litige, les parties s'efforceront de trouver une solution amiable. À défaut, les tribunaux algériens seront seuls compétents.</p>
                </div>
            </section>

        </div>

        <p class="text-center text-gray-500 text-sm mt-8">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
    </div>
</div>
@endsection
