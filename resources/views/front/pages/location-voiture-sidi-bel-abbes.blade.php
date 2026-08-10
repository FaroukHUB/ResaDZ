@extends('front.layouts.app')

@php
$city = 'Sidi Bel Abbès';
$slug = 'sidi-bel-abbes';
$metaTitle = 'Location voiture Sidi Bel Abbès — Loueurs vérifiés dès 4 000 DA/jour | ResaDZ';
$metaDescription = 'Louez une voiture à Sidi Bel Abbès auprès de loueurs vérifiés : tarifs 2026, Oran à 1h, réservation en ligne sur ResaDZ.';
$heroSubtitle = 'Comparez les loueurs vérifiés à Sidi Bel Abbès. Capitale de la Mékerra, à une heure d\'Oran et aux portes du Sud-Ouest.';

$intro = [
    'Ville universitaire et carrefour de l\'Ouest algérien, Sidi Bel Abbès relie Oran, Tlemcen, Mascara et les portes du Sud. L\'université Djillali Liabès, le pôle électronique et le tissu commercial de la Mékerra génèrent un vrai besoin de mobilité — et sans voiture, se déplacer entre les communes de la wilaya relève du parcours du combattant.',
    'ResaDZ vous met en relation directe avec des loueurs vérifiés à Sidi Bel Abbès. Véhicules contrôlés, prix affichés sans surprise, avis clients et réservation en ligne en quelques minutes.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 000 – 5 500 DA'],
    ['Berline', 'Logan, Sandero, Accent', '5 500 – 7 000 DA'],
    ['SUV', 'Duster, Stepway, Creta', '8 000 – 9 500 DA'],
    ['Van 7 places', 'Espace, Partner', '11 000 – 15 000 DA'],
];
$priceNote = 'Les tarifs bel-abbésiens comptent parmi les plus accessibles de l\'Ouest. Pour l\'été et les fêtes de fin d\'année, réservez à l\'avance — l\'offre locale reste plus limitée qu\'à Oran.';

$pickupTitle = 'Où récupérer votre voiture à Sidi Bel Abbès';
$pickupSpots = [
    ['Centre-ville', 'Remise en main propre au centre : place du 1er Novembre, jardin public ou gare routière, selon le point convenu avec le loueur.'],
    ['Université Djillali Liabès', 'Les loueurs desservent le campus et les cités universitaires — les locations étudiantes de moyenne durée sont courantes.'],
    ['Livraison aéroport d\'Oran', 'L\'aéroport Ahmed Ben Bella d\'Oran est à environ 1h par l\'autoroute : plusieurs loueurs proposent la livraison à l\'aérogare, supplément indiqué sur l\'annonce.'],
    ['Communes de la wilaya', 'Telagh, Sfisef, Ben Badis... certains loueurs acceptent la remise dans les communes proches — précisez votre besoin à la réservation.'],
];

$particulierTexte = [
    'À Sidi Bel Abbès, les agences de location se comptent sur les doigts d\'une main — la location via ResaDZ est la solution naturelle, avec des prix plus doux et un contact direct avec le propriétaire.',
    'Sur ResaDZ, chaque loueur est vérifié et chaque location donne lieu à un contrat. Caution, kilométrage et conditions figurent sur l\'annonce avant le paiement, et les avis des locataires précédents vous permettent de louer en toute confiance.',
];

$faqs = [
    ['q' => 'Quel est le prix d\'une location de voiture à Sidi Bel Abbès ?', 'a' => 'Environ 4 000 à 5 500 DA/jour pour une citadine, 5 500 à 7 000 DA pour une berline et 8 000 à 9 500 DA pour un SUV — parmi les tarifs les plus bas de l\'Ouest algérien.'],
    ['q' => 'Peut-on se faire livrer la voiture à l\'aéroport d\'Oran ?', 'a' => 'Oui, plusieurs loueurs bel-abbésiens livrent à l\'aéroport Ahmed Ben Bella d\'Oran, à environ une heure par l\'autoroute Est-Ouest. Le supplément est indiqué sur l\'annonce.'],
    ['q' => 'Peut-on rouler vers Tlemcen ou le Sud avec la location ?', 'a' => 'Oui, sauf restriction mentionnée sur l\'annonce. Pour les longs trajets vers Béchar ou le Sud-Ouest, signalez votre itinéraire au loueur et vérifiez le kilométrage inclus.'],
    ['q' => 'Quels documents faut-il pour louer à Sidi Bel Abbès ?', 'a' => 'Permis de conduire valide (2 ans d\'ancienneté en général), pièce d\'identité ou passeport, et caution dont le montant est affiché sur chaque annonce.'],
    ['q' => 'Y a-t-il des locations longue durée pour étudiants ?', 'a' => 'Oui, l\'université Djillali Liabès génère une vraie demande : au-delà de 10 jours les tarifs sont dégressifs, et certains loueurs proposent des formules mensuelles.'],
    ['q' => 'Peut-on louer avec chauffeur à Sidi Bel Abbès ?', 'a' => 'Certains loueurs proposent l\'option chauffeur pour les mariages et déplacements professionnels. L\'option et son tarif figurent sur l\'annonce.'],
];

$nearby = [
    '/location-voiture-oran' => 'Location voiture Oran',
    '/location-voiture-tlemcen' => 'Location voiture Tlemcen',
    '/location-voiture-alger' => 'Location voiture Alger',
    '/location-voiture-blida' => 'Location voiture Blida',
];
@endphp

@include('front.pages.partials.wilaya-seo-page')
