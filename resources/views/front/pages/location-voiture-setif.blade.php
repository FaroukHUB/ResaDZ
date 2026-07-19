@extends('front.layouts.app')

@php
$city = 'Sétif';
$slug = 'setif';
$metaTitle = 'Location voiture Sétif — Loueurs vérifiés dès 4 000 DA/jour | ResaDZ';
$metaDescription = 'Louez une voiture à Sétif entre particuliers : tarifs réels 2026, loueurs vérifiés, El Eulma, Aïn El Fouara, Djemila. Réservation en ligne sur ResaDZ.';
$heroSubtitle = 'Comparez les loueurs particuliers vérifiés à Sétif et El Eulma. Capitale des hauts plateaux, carrefour commercial de l\'Est algérien.';

$intro = [
    'Sétif est la capitale économique des hauts plateaux : le commerce d\'El Eulma, le Park Mall, l\'université Ferhat Abbas et les ruines romaines de Djemila attirent chaque jour des milliers de visiteurs. Dans une wilaya aussi étendue — de Bougaâ aux portes de Bordj Bou Arréridj — la voiture reste le seul moyen de se déplacer efficacement.',
    'ResaDZ regroupe les loueurs particuliers vérifiés de la wilaya de Sétif. Comparez les véhicules, les prix réels et les avis clients, puis réservez en ligne. Contact direct avec le propriétaire, contrat automatique, zéro commission d\'agence.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 000 – 5 500 DA'],
    ['Berline', 'Logan, Sandero, Elantra', '5 500 – 7 500 DA'],
    ['SUV', 'Duster, Stepway, Tucson', '8 000 – 10 000 DA'],
    ['Van 7 places', 'Espace, Expert', '11 000 – 16 000 DA'],
];
$priceNote = 'Le marché sétifien est dynamique grâce au commerce d\'El Eulma : l\'offre est large et les prix compétitifs. Pendant les fêtes et la saison des mariages, réservez tôt.';

$pickupTitle = 'Où récupérer votre voiture à Sétif';
$pickupSpots = [
    ['Centre-ville de Sétif', 'Remise en main propre près de Aïn El Fouara, du Park Mall ou de la gare routière — le point exact est convenu avec le loueur à la réservation.'],
    ['El Eulma', 'Deuxième pôle de la wilaya et grand centre commercial : plusieurs loueurs y sont basés, pratique pour les commerçants de passage à Dubaï Market.'],
    ['Université et cité El Bez', 'Les loueurs desservent le campus Ferhat Abbas et les cités universitaires pour les locations étudiantes ou familiales.'],
    ['Livraison aéroport', 'L\'aéroport de Sétif (8 Mai 1945) et celui de Constantine (1h15) sont couverts par certains loueurs — vérifiez l\'option livraison sur l\'annonce.'],
];

$particulierTexte = [
    'À Sétif, la location entre particuliers est portée par une vraie culture du commerce : les propriétaires soignent leurs véhicules et la concurrence tire les prix vers le bas, loin des tarifs des agences classiques.',
    'Sur ResaDZ, chaque loueur est vérifié (identité et papiers du véhicule) et chaque location est encadrée par un contrat. Caution, kilométrage et conditions sont affichés avant la réservation, et les avis clients vous guident vers les meilleurs loueurs de la wilaya.',
];

$faqs = [
    ['q' => 'Quel est le prix d\'une location de voiture à Sétif ?', 'a' => 'Comptez 4 000 à 5 500 DA/jour pour une citadine, 5 500 à 7 500 DA pour une berline et 8 000 à 10 000 DA pour un SUV. Les tarifs baissent au-delà de 10 jours de location.'],
    ['q' => 'Peut-on louer une voiture à El Eulma ?', 'a' => 'Oui, plusieurs loueurs de la plateforme sont basés à El Eulma. C\'est pratique pour les commerçants et visiteurs du marché — filtrez par wilaya de Sétif et vérifiez la commune du loueur.'],
    ['q' => 'Peut-on aller à Djemila avec la voiture de location ?', 'a' => 'Oui, le site romain de Djemila est à environ 50 km de Sétif par une route correcte. Signalez simplement votre itinéraire au loueur et vérifiez le kilométrage inclus.'],
    ['q' => 'Quels documents faut-il pour louer à Sétif ?', 'a' => 'Permis de conduire valide (2 ans d\'ancienneté en général), pièce d\'identité ou passeport, et caution dont le montant est précisé sur chaque annonce.'],
    ['q' => 'Les voitures supportent-elles l\'hiver des hauts plateaux ?', 'a' => 'Les loueurs sétifiens connaissent leur climat : véhicules entretenus pour le froid et la neige occasionnelle. En hiver, vérifiez l\'état des pneus à la remise des clés.'],
    ['q' => 'Y a-t-il des locations avec chauffeur à Sétif ?', 'a' => 'Oui, certains loueurs proposent l\'option chauffeur, notamment pour les mariages et les déplacements professionnels. L\'option est indiquée sur l\'annonce.'],
];

$nearby = [
    '/location-voiture-aeroport-constantine' => 'Location aéroport Constantine',
    '/location-voiture-constantine' => 'Location voiture Constantine',
    '/location-voiture-bejaia' => 'Location voiture Béjaïa',
    '/location-voiture-alger' => 'Location voiture Alger',
    '/location-voiture-annaba' => 'Location voiture Annaba',
];
@endphp

@include('front.pages.partials.wilaya-seo-page')
