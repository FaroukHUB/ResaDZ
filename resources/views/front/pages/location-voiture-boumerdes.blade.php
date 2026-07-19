@extends('front.layouts.app')

@php
$city = 'Boumerdès';
$slug = 'boumerdes';
$metaTitle = 'Location voiture Boumerdès — Loueurs vérifiés dès 4 000 DA/jour | ResaDZ';
$metaDescription = 'Louez une voiture à Boumerdès entre particuliers : tarifs 2026, loueurs vérifiés, plages et côte est d\'Alger. Réservation en ligne sur ResaDZ.';
$heroSubtitle = 'Comparez les loueurs particuliers vérifiés à Boumerdès. Plages, université, côte est — à 30 minutes de l\'aéroport d\'Alger.';

$intro = [
    'Entre mer et université, Boumerdès vit au rythme de sa côte : Rocher Noir, Boudouaou-El-Bahri, Zemmouri El Bahri, Cap Djinet... Autant de plages et de localités que les transports publics desservent mal. L\'été surtout, une voiture de location est le seul moyen de profiter réellement du littoral est-algérois sans dépendre des taxis.',
    'ResaDZ réunit les loueurs particuliers vérifiés de la wilaya de Boumerdès : véhicules contrôlés, prix affichés, avis clients et réservation en ligne. Vous traitez directement avec le propriétaire, sans commission d\'agence.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, Picanto', '4 000 – 5 500 DA'],
    ['Berline', 'Logan, Sandero, i20', '5 500 – 7 000 DA'],
    ['SUV', 'Duster, Stepway, Tucson', '8 000 – 10 000 DA'],
    ['Van 7 places', 'Espace, Caddy', '11 000 – 16 000 DA'],
];
$priceNote = 'En juillet-août, la demande explose sur la côte de Boumerdès : les prix montent de 20 à 30% et les meilleurs véhicules partent vite. Réservez à l\'avance pour la saison estivale.';

$pickupTitle = 'Où récupérer votre voiture à Boumerdès';
$pickupSpots = [
    ['Boumerdès ville', 'Remise en main propre au centre-ville, près de l\'université M\'hamed Bougara ou du front de mer, selon le point convenu avec le loueur.'],
    ['Boudouaou et l\'axe autoroutier', 'Les loueurs de Boudouaou, Ouled Moussa et Khemis El Khechna sont pratiques si vous arrivez d\'Alger par l\'autoroute A1.'],
    ['Livraison aéroport d\'Alger', 'L\'aéroport Houari Boumédiène est à 30 minutes : la plupart des loueurs livrent directement à l\'aérogare, supplément indiqué sur l\'annonce.'],
    ['Côte est (Zemmouri, Dellys)', 'Pour un séjour balnéaire, certains loueurs acceptent la remise à Zemmouri El Bahri, Cap Djinet ou Dellys — précisez-le lors de la réservation.'],
];

$particulierTexte = [
    'À Boumerdès, l\'offre d\'agences classiques est limitée — la location entre particuliers est devenue le réflexe local, avec des prix nettement plus accessibles et un vrai contact humain.',
    'Sur ResaDZ, chaque loueur est vérifié et chaque réservation est encadrée par un contrat. Conditions, caution et kilométrage sont affichés sur l\'annonce avant de payer. Les avis des locataires précédents vous aident à choisir le bon véhicule pour la côte ou le quotidien.',
];

$faqs = [
    ['q' => 'Quel est le prix d\'une location de voiture à Boumerdès ?', 'a' => 'Environ 4 000 à 5 500 DA/jour pour une citadine, 5 500 à 7 000 DA pour une berline et 8 000 à 10 000 DA pour un SUV. Comptez 20 à 30% de plus en juillet-août sur la côte.'],
    ['q' => 'Peut-on se faire livrer à l\'aéroport d\'Alger ?', 'a' => 'Oui — l\'aéroport Houari Boumédiène n\'est qu\'à 30 minutes de Boumerdès par l\'autoroute. La majorité des loueurs proposent la livraison à l\'aérogare, souvent avec un petit supplément.'],
    ['q' => 'Faut-il réserver à l\'avance pour l\'été ?', 'a' => 'Fortement conseillé. La saison balnéaire (juin à septembre) sature le marché local : les véhicules les mieux notés se réservent plusieurs semaines à l\'avance.'],
    ['q' => 'Quels documents faut-il pour louer à Boumerdès ?', 'a' => 'Permis de conduire valide (2 ans d\'ancienneté en général), pièce d\'identité ou passeport, et caution dont le montant est affiché sur chaque annonce.'],
    ['q' => 'Peut-on rouler jusqu\'à Dellys ou la Kabylie avec la location ?', 'a' => 'Oui, sauf mention contraire sur l\'annonce. Vérifiez le kilométrage inclus et signalez au loueur vos trajets prévus si vous sortez de la wilaya.'],
    ['q' => 'Y a-t-il des locations longue durée à Boumerdès ?', 'a' => 'Oui, notamment pour les étudiants et enseignants de l\'université. Au-delà de 10 jours, les loueurs appliquent des tarifs dégressifs — comparez les offres mensuelles.'],
];

$nearby = [
    '/location-voiture-alger' => 'Location voiture Alger',
    '/location-voiture-aeroport-alger' => 'Location aéroport Alger',
    '/location-voiture-blida' => 'Location voiture Blida',
    '/location-voiture-tizi-ouzou' => 'Location voiture Tizi Ouzou',
];
@endphp

@include('front.pages.partials.wilaya-seo-page')
