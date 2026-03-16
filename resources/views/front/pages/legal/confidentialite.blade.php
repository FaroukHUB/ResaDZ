@extends('front.layouts.app')

@section('title', 'Politique de Confidentialité - ' . $companyName)
@section('meta_description', 'Politique de confidentialité et protection des données personnelles de ' . $companyName . ', marketplace de location de véhicules en Algérie.')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Politique de Confidentialité</h1>
            <p class="text-gray-300">Protection de vos données personnelles</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction</h2>
                <div class="prose prose-gray max-w-none">
                    <p>{{ $companyName }} s'engage à protéger la vie privée de ses utilisateurs. La présente Politique de Confidentialité explique comment nous collectons, utilisons, stockons et protégeons vos données personnelles lorsque vous utilisez notre plateforme.</p>
                    <p class="mt-4">En utilisant {{ $companyName }}, vous acceptez les pratiques décrites dans la présente politique.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Données collectées</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous collectons les types de données suivants :</p>

                    <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">2.1 Données fournies directement</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Identité :</strong> nom, prénom</li>
                        <li><strong>Coordonnées :</strong> adresse email, numéro de téléphone, adresse postale</li>
                        <li><strong>Documents :</strong> permis de conduire, pièce d'identité (pour les réservations)</li>
                        <li><strong>Informations de paiement :</strong> données nécessaires au traitement des paiements</li>
                    </ul>

                    <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">2.2 Données collectées automatiquement</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Données de navigation :</strong> adresse IP, type de navigateur, pages visitées</li>
                        <li><strong>Données d'appareil :</strong> type d'appareil, système d'exploitation</li>
                        <li><strong>Cookies :</strong> identifiants de session, préférences</li>
                        <li><strong>Données de géolocalisation :</strong> localisation approximative (si autorisée)</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Finalités du traitement</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous utilisons vos données pour :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Fournir nos services :</strong> gestion des réservations, communication entre loueurs et locataires</li>
                        <li><strong>Améliorer la plateforme :</strong> analyse d'utilisation, développement de nouvelles fonctionnalités</li>
                        <li><strong>Communiquer avec vous :</strong> confirmations de réservation, notifications, support client</li>
                        <li><strong>Marketing :</strong> newsletters, offres promotionnelles (avec votre consentement)</li>
                        <li><strong>Sécurité :</strong> prévention de la fraude, protection des utilisateurs</li>
                        <li><strong>Obligations légales :</strong> respect des réglementations en vigueur</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Base juridique du traitement</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous traitons vos données sur les bases juridiques suivantes :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Exécution du contrat :</strong> pour fournir les services de réservation</li>
                        <li><strong>Consentement :</strong> pour les communications marketing et les cookies non essentiels</li>
                        <li><strong>Intérêt légitime :</strong> pour améliorer nos services et assurer la sécurité</li>
                        <li><strong>Obligation légale :</strong> pour respecter les réglementations applicables</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Partage des données</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Vos données peuvent être partagées avec :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Les Loueurs :</strong> informations nécessaires à la réservation (nom, contact, documents)</li>
                        <li><strong>Prestataires de services :</strong> hébergement, paiement, envoi d'emails</li>
                        <li><strong>Autorités :</strong> en cas d'obligation légale ou de réquisition judiciaire</li>
                    </ul>
                    <p class="mt-4">Nous ne vendons jamais vos données personnelles à des tiers.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Conservation des données</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous conservons vos données pendant les durées suivantes :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Données de compte :</strong> pendant la durée de votre inscription + 3 ans après suppression</li>
                        <li><strong>Données de réservation :</strong> 10 ans (obligations comptables et fiscales)</li>
                        <li><strong>Données de navigation :</strong> 13 mois maximum</li>
                        <li><strong>Documents d'identité :</strong> durée de la réservation + 1 an</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Sécurité des données</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous mettons en œuvre des mesures techniques et organisationnelles pour protéger vos données :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Chiffrement des données sensibles (SSL/TLS)</li>
                        <li>Accès restreint aux données personnelles</li>
                        <li>Surveillance et détection des intrusions</li>
                        <li>Sauvegardes régulières</li>
                        <li>Formation du personnel à la protection des données</li>
                    </ul>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Vos droits</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Conformément à la réglementation applicable, vous disposez des droits suivants :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Droit d'accès :</strong> obtenir une copie de vos données personnelles</li>
                        <li><strong>Droit de rectification :</strong> corriger des données inexactes ou incomplètes</li>
                        <li><strong>Droit à l'effacement :</strong> demander la suppression de vos données</li>
                        <li><strong>Droit à la limitation :</strong> limiter le traitement de vos données</li>
                        <li><strong>Droit à la portabilité :</strong> recevoir vos données dans un format structuré</li>
                        <li><strong>Droit d'opposition :</strong> vous opposer au traitement de vos données</li>
                        <li><strong>Droit de retrait du consentement :</strong> retirer votre consentement à tout moment</li>
                    </ul>
                    <p class="mt-4">Pour exercer ces droits, contactez-nous à : <a href="mailto:{{ $companyEmail }}" class="text-green-600 hover:underline">{{ $companyEmail }}</a></p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Cookies</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous utilisons des cookies pour :</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Cookies essentiels :</strong> fonctionnement du site, sécurité, authentification</li>
                        <li><strong>Cookies de performance :</strong> analyse d'utilisation, amélioration du service</li>
                        <li><strong>Cookies de fonctionnalité :</strong> mémorisation de vos préférences</li>
                        <li><strong>Cookies marketing :</strong> publicités personnalisées (avec votre consentement)</li>
                    </ul>
                    <p class="mt-4">Vous pouvez gérer vos préférences de cookies via les paramètres de votre navigateur.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Transferts internationaux</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Vos données sont principalement stockées en Algérie. Si un transfert vers d'autres pays est nécessaire (prestataires techniques), nous nous assurons que des garanties appropriées sont en place pour protéger vos données.</p>
                </div>
            </section>

            <section class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Modifications de la politique</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Nous pouvons modifier cette Politique de Confidentialité à tout moment. Les modifications seront publiées sur cette page avec une date de mise à jour. Nous vous encourageons à consulter régulièrement cette page.</p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Contact</h2>
                <div class="prose prose-gray max-w-none">
                    <p>Pour toute question concernant cette Politique de Confidentialité ou pour exercer vos droits, contactez-nous :</p>
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p><strong>{{ $companyName }}</strong></p>
                        <p>Email : <a href="mailto:{{ $companyEmail }}" class="text-green-600 hover:underline">{{ $companyEmail }}</a></p>
                    </div>
                </div>
            </section>

        </div>

        <p class="text-center text-gray-500 text-sm mt-8">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>
    </div>
</div>
@endsection
