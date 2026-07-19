@extends('front.layouts.app')

@php
$city = 'Annaba';
$slug = 'annaba';
$airportName = 'Rabah Bitat';
$airportCode = 'AAE';
$metaTitle = 'Location voiture aéroport Annaba (Rabah Bitat) — Livraison au terminal | ResaDZ';
$metaDescription = 'Louez une voiture à l\'aéroport d\'Annaba Rabah Bitat : livraison au terminal, loueurs vérifiés, réservation en ligne. Dès 4 500 DA/jour.';
$heroSubtitle = 'Votre voiture vous attend à l\'aéroport Rabah Bitat (Les Salines). Cap sur la Corniche annabie, Séraïdi ou El Tarf sans attendre.';

$intro = [
    'L\'aéroport Rabah Bitat (Les Salines) relie Annaba à Paris, Marseille et Lyon. À l\'atterrissage, la Corniche, Séraïdi, Chetaïbi ou les plages d\'El Tarf n\'attendent pas — mais sans voiture, l\'Est extrême algérien se découvre mal : les taxis se font rares et les distances sont réelles.',
    'Avec ResaDZ, vous réservez avant votre vol auprès d\'un loueur particulier vérifié d\'Annaba. Il vous accueille à l\'aérogare avec le véhicule et le contrat : quelques minutes après la douane, vous roulez vers Saint-Cloud, la basilique ou la route de Séraïdi.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 500 – 6 000 DA'],
    ['Berline', 'Logan, Sandero, Accent', '6 000 – 7 500 DA'],
    ['SUV', 'Duster, Stepway, Tucson', '8 500 – 10 000 DA'],
    ['Van 7 places', 'Espace, Partner', '12 000 – 16 000 DA'],
];
$priceNote = 'L\'été, la diaspora annabie et les visiteurs tunisiens font monter la demande — réservez avant juillet pour garder le choix des véhicules.';

$deliveryText = [
    'La livraison à l\'aéroport Rabah Bitat est proposée par la plupart des loueurs annabis, gratuitement ou avec un supplément modéré affiché sur l\'annonce. Communiquez votre numéro de vol (AAE) : votre arrivée est suivie même en cas de retard.',
    'La remise se fait devant l\'aérogare des Salines : état des lieux, signature, remise des clés en une quinzaine de minutes. Pour le vol retour, convenez de la restitution à l\'aéroport avec 30 minutes de marge avant l\'enregistrement.',
];

$faqs = [
    ['q' => 'Combien coûte une location à l\'aéroport d\'Annaba ?', 'a' => 'De 4 500 DA/jour pour une citadine à 10 000 DA pour un SUV récent. Le supplément de livraison aéroport éventuel est indiqué sur chaque annonce.'],
    ['q' => 'Peut-on aller jusqu\'à El Tarf, El Kala ou la frontière tunisienne ?', 'a' => 'Oui, sauf restriction sur l\'annonce. Signalez au loueur si vous comptez passer en Tunisie — la sortie du territoire est généralement exclue.'],
    ['q' => 'La route de Séraïdi est-elle accessible avec une citadine ?', 'a' => 'Oui, la route est goudronnée et praticable, mais en hiver un SUV est plus confortable dans les lacets de l\'Edough.'],
    ['q' => 'Les permis français ou étrangers sont-ils acceptés ?', 'a' => 'Oui, la majorité des loueurs acceptent les permis européens, parfois avec le permis international en complément — précisé sur l\'annonce.'],
    ['q' => 'Le loueur attend-il en cas de retard de vol ?', 'a' => 'Oui — avec votre numéro de vol, le loueur suit l\'horaire réel d\'atterrissage à Rabah Bitat et adapte le rendez-vous.'],
    ['q' => 'Peut-on restituer la voiture à l\'aéroport avant le retour ?', 'a' => 'Oui, la restitution aux Salines se convient à la réservation. Prévoyez 30 minutes pour l\'état des lieux avant votre enregistrement.'],
];

$nearby = [
    '/location-voiture-annaba' => 'Location voiture Annaba',
    '/location-voiture-constantine' => 'Location voiture Constantine',
    '/location-voiture-setif' => 'Location voiture Sétif',
    '/location-voiture-aeroport-alger' => 'Location aéroport Alger',
];
@endphp

@include('front.pages.partials.aeroport-seo-page')
