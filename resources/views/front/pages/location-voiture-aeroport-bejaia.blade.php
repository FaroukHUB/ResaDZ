@extends('front.layouts.app')

@php
$city = 'Béjaïa';
$slug = 'bejaia';
$airportName = 'Abane Ramdane (Soummam)';
$airportCode = 'BJA';
$metaTitle = 'Location voiture aéroport Béjaïa (Soummam) — Livraison au terminal | ResaDZ';
$metaDescription = 'Louez une voiture à l\'aéroport de Béjaïa Soummam - Abane Ramdane : livraison au terminal, loueurs vérifiés, réservation en ligne. Dès 4 500 DA/jour.';
$heroSubtitle = 'Votre voiture vous attend à l\'aéroport Soummam - Abane Ramdane. Cap sur la Corniche kabyle, les Aiguades ou la vallée de la Soummam.';

$intro = [
    'L\'aéroport Abane Ramdane de Béjaïa accueille les vols de la diaspora kabyle depuis Paris, Marseille et Lyon. À l\'arrivée, tout le monde a le même objectif : rejoindre au plus vite la Corniche, Tichy, Aokas, ou remonter la vallée de la Soummam vers Akbou et Sidi Aïch — des trajets impossibles sans voiture.',
    'Avec ResaDZ, réservez avant votre vol auprès d\'un loueur particulier vérifié de Béjaïa : il vous livre le véhicule à l\'aérogare, contrat prêt. En été, c\'est la garantie d\'éviter la pénurie chronique de véhicules qui frappe la région à chaque saison estivale.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 500 – 6 000 DA'],
    ['Berline', 'Logan, Sandero, i20', '6 000 – 7 500 DA'],
    ['SUV', 'Duster, Stepway, Creta', '8 500 – 10 500 DA'],
    ['Van 7 places', 'Espace, Caddy', '12 000 – 17 000 DA'],
];
$priceNote = 'Béjaïa est l\'une des destinations les plus tendues de l\'été algérien : en juillet-août les prix montent de 25 à 35% et les véhicules partent très vite. Réservez dès que vos billets sont pris.';

$deliveryText = [
    'Les loueurs béjaouis livrent à l\'aéroport Soummam gratuitement ou avec un léger supplément affiché sur l\'annonce. Indiquez votre numéro de vol (BJA) à la réservation pour que le loueur suive votre arrivée, y compris en cas de retard.',
    'La remise du véhicule se fait à la sortie de l\'aérogare : état des lieux, signature du contrat, remise des clés — comptez un quart d\'heure. La restitution au retour se convient de la même manière, avec une marge de 30 minutes avant votre enregistrement.',
];

$faqs = [
    ['q' => 'Combien coûte une location à l\'aéroport de Béjaïa ?', 'a' => 'De 4 500 DA/jour pour une citadine à 10 500 DA pour un SUV. En haute saison estivale, comptez 25 à 35% de plus — la Corniche kabyle est très demandée.'],
    ['q' => 'Faut-il réserver longtemps à l\'avance pour l\'été ?', 'a' => 'Oui, c\'est indispensable à Béjaïa : les véhicules d\'été se réservent dès le printemps. Réservez dès l\'achat de vos billets d\'avion.'],
    ['q' => 'Peut-on rouler sur la Corniche et vers Tichy, Aokas, Souk El Tenine ?', 'a' => 'Oui, tous ces trajets côtiers sont classiques. Vérifiez le kilométrage inclus sur l\'annonce si vous prévoyez aussi la vallée de la Soummam.'],
    ['q' => 'Les permis français sont-ils acceptés ?', 'a' => 'Oui, la majorité des loueurs acceptent le permis français ou européen, parfois accompagné du permis international — précisé sur chaque annonce.'],
    ['q' => 'Le loueur m\'attend-il si le vol est retardé ?', 'a' => 'Oui, en transmettant votre numéro de vol, le loueur suit l\'horaire réel d\'atterrissage à Abane Ramdane et vous attend.'],
    ['q' => 'Peut-on restituer le véhicule à l\'aéroport ?', 'a' => 'Oui, la restitution à l\'aérogare avant votre vol retour se convient à la réservation. Prévoyez 30 minutes pour l\'état des lieux.'],
];

$nearby = [
    '/location-voiture-bejaia' => 'Location voiture Béjaïa',
    '/location-voiture-tizi-ouzou' => 'Location voiture Tizi Ouzou',
    '/location-voiture-setif' => 'Location voiture Sétif',
    '/location-voiture-aeroport-alger' => 'Location aéroport Alger',
];
@endphp

@include('front.pages.partials.aeroport-seo-page')
