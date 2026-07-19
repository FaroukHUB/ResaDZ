@extends('front.layouts.app')

@php
$city = 'Blida';
$slug = 'blida';
$metaTitle = 'Location voiture Blida — Loueurs vérifiés dès 4 000 DA/jour | ResaDZ';
$metaDescription = 'Louez une voiture à Blida entre particuliers : tarifs réels 2026, loueurs vérifiés, réservation en ligne. La Mitidja, Chréa et Alger à portée de route.';
$heroSubtitle = 'Comparez les loueurs particuliers vérifiés à Blida et dans la Mitidja. Prix transparents, contrat automatique, à 45 minutes d\'Alger.';

$intro = [
    'Blida, la ville des Roses, est le carrefour naturel entre Alger, la Mitidja agricole et la montagne de Chréa. Que vous veniez pour un mariage, une visite familiale, un déplacement professionnel ou des vacances au parc national de Chréa, disposer d\'une voiture sur place change tout : les transports en commun ne couvrent ni les horaires ni les trajets dont vous avez réellement besoin.',
    'ResaDZ vous connecte directement avec des loueurs particuliers vérifiés dans la wilaya de Blida. Vous comparez les véhicules, les prix et les avis, puis vous réservez en ligne en quelques minutes — sans agence ni intermédiaire.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 000 – 5 500 DA'],
    ['Berline', 'Logan, Sandero, Accent', '5 500 – 7 000 DA'],
    ['SUV', 'Duster, Stepway, Creta', '8 000 – 9 500 DA'],
    ['Van 7 places', 'Espace, Partner', '11 000 – 16 000 DA'],
];
$priceNote = 'Tarifs du marché entre particuliers à Blida, généralement inférieurs de 10 à 15% à ceux d\'Alger. En saison des mariages (été) et pendant les fêtes, réservez plusieurs semaines à l\'avance.';

$pickupTitle = 'Où récupérer votre voiture à Blida';
$pickupSpots = [
    ['Centre-ville de Blida', 'Remise en main propre au centre : placette Ettoute, Bab Essebt ou près de la gare. Le loueur convient du point de rendez-vous avec vous à la réservation.'],
    ['Boufarik et la Mitidja', 'De nombreux loueurs couvrent Boufarik, Mouzaïa, El Affroun et Beni Mered — pratique si vous arrivez par l\'autoroute A1.'],
    ['Livraison aéroport d\'Alger', 'La plupart des loueurs blidéens livrent à l\'aéroport Houari Boumédiène (45 min par l\'autoroute), souvent avec un supplément raisonnable indiqué sur l\'annonce.'],
    ['Chréa et environs', 'Pour un séjour à la montagne, certains loueurs proposent des SUV adaptés à la route de Chréa — vérifiez l\'option sur l\'annonce.'],
];

$particulierTexte = [
    'La location entre particuliers s\'est imposée à Blida comme l\'alternative économique aux agences : les prix sont plus bas, le contact est direct et les véhicules sont souvent plus récents que dans les petites agences locales.',
    'Sur ResaDZ, chaque loueur est vérifié (identité, documents du véhicule) et chaque location donne lieu à un contrat. Les avis laissés par les clients précédents vous permettent de choisir en confiance. La caution et les conditions sont affichées sur l\'annonce avant la réservation — aucune surprise le jour J.',
];

$faqs = [
    ['q' => 'Quel est le prix d\'une location de voiture à Blida ?', 'a' => 'Comptez 4 000 à 5 500 DA/jour pour une citadine, 5 500 à 7 000 DA pour une berline et 8 000 à 9 500 DA pour un SUV. Les prix baissent pour les locations de longue durée (plus de 10 jours).'],
    ['q' => 'Peut-on se faire livrer la voiture à l\'aéroport d\'Alger depuis Blida ?', 'a' => 'Oui, la plupart des loueurs de Blida proposent la livraison à l\'aéroport Houari Boumédiène, à 45 minutes par l\'autoroute. Le supplément éventuel est indiqué sur l\'annonce.'],
    ['q' => 'Quels documents faut-il pour louer une voiture à Blida ?', 'a' => 'Un permis de conduire valide (généralement 2 ans d\'ancienneté minimum), une pièce d\'identité ou un passeport, et une caution dont le montant est précisé sur chaque annonce.'],
    ['q' => 'Peut-on louer une voiture à Blida pour un mariage ?', 'a' => 'Oui, c\'est une demande très courante à Blida. Berlines et SUV récents sont disponibles ; réservez tôt en été car la saison des mariages sature le marché local.'],
    ['q' => 'La voiture peut-elle monter à Chréa ?', 'a' => 'Vérifiez l\'annonce : certains loueurs excluent la route de montagne, d\'autres proposent des SUV adaptés. En hiver, les chaînes peuvent être exigées par la gendarmerie.'],
    ['q' => 'Peut-on louer sans chauffeur à Blida ?', 'a' => 'Oui, la majorité des annonces sur ResaDZ sont sans chauffeur. Des loueurs proposent aussi une option avec chauffeur si vous préférez.'],
];

$nearby = [
    '/location-voiture-alger' => 'Location voiture Alger',
    '/location-voiture-aeroport-alger' => 'Location aéroport Alger',
    '/location-voiture-boumerdes' => 'Location voiture Boumerdès',
    '/location-voiture-oran' => 'Location voiture Oran',
];
@endphp

@include('front.pages.partials.wilaya-seo-page')
