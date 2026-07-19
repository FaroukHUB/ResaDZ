@extends('front.layouts.app')

@php
$city = 'Oran';
$slug = 'oran';
$airportName = 'Ahmed Ben Bella';
$airportCode = 'ORN';
$metaTitle = 'Location voiture aéroport Oran (Ahmed Ben Bella) — Livraison au terminal | ResaDZ';
$metaDescription = 'Louez une voiture à l\'aéroport d\'Oran Ahmed Ben Bella : livraison au terminal, loueurs vérifiés, réservation en ligne. Dès 4 500 DA/jour.';
$heroSubtitle = 'Votre voiture vous attend à l\'aéroport Ahmed Ben Bella (Es Sénia). Réservez avant le vol, le loueur vous accueille à l\'atterrissage.';

$intro = [
    'L\'aéroport international Ahmed Ben Bella (Es Sénia) est la porte d\'entrée de tout l\'Ouest algérien. Vols depuis Paris, Marseille, Lyon, Alicante ou Barcelone — chaque jour, des centaines de voyageurs atterrissent à Oran et cherchent le même service : une voiture qui les attend, sans file d\'attente ni navette.',
    'Avec ResaDZ, vous réservez avant votre vol auprès d\'un loueur particulier vérifié d\'Oran, et il vous livre le véhicule directement à l\'aérogare. Contrat prêt, état des lieux rapide, et vous voilà sur la route d\'Es Sénia, du front de mer ou d\'Aïn El Turck en quelques minutes.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 500 – 6 000 DA'],
    ['Berline', 'Logan, Sandero, Accent', '6 000 – 7 500 DA'],
    ['SUV', 'Duster, Stepway, Tucson', '8 500 – 10 000 DA'],
    ['Van 7 places', 'Espace, Caddy', '12 000 – 17 000 DA'],
];
$priceNote = 'En été (juin à septembre), la demande de la diaspora fait grimper les prix de 20 à 30% : réservez plusieurs semaines avant votre vol pour garder le choix.';

$deliveryText = [
    'La plupart des loueurs oranais proposent la livraison à l\'aéroport Ahmed Ben Bella gratuitement ou avec un supplément modéré (généralement 1 000 à 2 000 DA), indiqué clairement sur l\'annonce. Communiquez votre numéro de vol lors de la réservation : le loueur suit votre arrivée et vous attend même en cas de retard.',
    'Le rendez-vous se fait à la sortie du terminal ou au parking de l\'aérogare. Prévoyez 10 à 15 minutes pour l\'état des lieux et la signature du contrat avant de prendre la route. Pour le retour, la restitution à l\'aéroport se convient de la même façon avant votre vol.',
];

$faqs = [
    ['q' => 'Combien coûte une location de voiture à l\'aéroport d\'Oran ?', 'a' => 'Les tarifs démarrent à 4 500 DA/jour pour une citadine et vont jusqu\'à 10 000 DA pour un SUV récent. La livraison à l\'aérogare est souvent offerte ou facturée 1 000 à 2 000 DA.'],
    ['q' => 'Le loueur m\'attend-il si mon vol est en retard ?', 'a' => 'Oui — en communiquant votre numéro de vol à la réservation, le loueur suit l\'horaire réel de votre arrivée à Ahmed Ben Bella et s\'adapte en cas de retard.'],
    ['q' => 'Peut-on louer avec un permis français à Oran ?', 'a' => 'Oui, la majorité des loueurs acceptent le permis français ou européen, parfois accompagné du permis international. La condition exacte figure sur chaque annonce.'],
    ['q' => 'Peut-on rendre la voiture à l\'aéroport avant le vol retour ?', 'a' => 'Oui, la restitution à l\'aérogare se convient avec le loueur lors de la réservation. Prévoyez 30 minutes de marge pour l\'état des lieux avant l\'enregistrement.'],
    ['q' => 'Faut-il payer une caution ?', 'a' => 'Oui, comme partout en Algérie. Le montant et le mode (espèces ou empreinte) sont affichés sur l\'annonce — préparez-le avant votre départ.'],
    ['q' => 'Peut-on aller à Aïn El Turck, Mostaganem ou Tlemcen avec la location ?', 'a' => 'Oui, sauf restriction indiquée sur l\'annonce. Vérifiez le kilométrage inclus et mentionnez vos trajets prévus au loueur.'],
];

$nearby = [
    '/location-voiture-oran' => 'Location voiture Oran',
    '/location-voiture-sidi-bel-abbes' => 'Location voiture Sidi Bel Abbès',
    '/location-voiture-tlemcen' => 'Location voiture Tlemcen',
    '/location-voiture-aeroport-alger' => 'Location aéroport Alger',
];
@endphp

@include('front.pages.partials.aeroport-seo-page')
