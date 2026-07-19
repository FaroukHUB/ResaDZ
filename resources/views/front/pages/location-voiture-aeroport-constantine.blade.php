@extends('front.layouts.app')

@php
$city = 'Constantine';
$slug = 'constantine';
$airportName = 'Mohamed Boudiaf';
$airportCode = 'CZL';
$metaTitle = 'Location voiture aéroport Constantine (Mohamed Boudiaf) | ResaDZ';
$metaDescription = 'Louez une voiture à l\'aéroport de Constantine Mohamed Boudiaf : livraison au terminal, loueurs vérifiés, réservation en ligne. Dès 4 500 DA/jour.';
$heroSubtitle = 'Votre voiture vous attend à l\'aéroport Mohamed Boudiaf. Réservez avant le vol et prenez la route des ponts suspendus sans attendre.';

$intro = [
    'L\'aéroport Mohamed Boudiaf dessert tout l\'Est constantinois : vols réguliers depuis Paris, Marseille, Lyon et Istanbul. À l\'arrivée, les solutions de transport sont limitées — les taxis se négocient et aucune navette ne dessert correctement Ali Mendjeli, El Khroub ou les wilayas voisines de Mila et Guelma.',
    'Avec ResaDZ, vous réservez votre voiture avant d\'embarquer auprès d\'un loueur particulier vérifié de Constantine. Il vous attend à l\'aérogare avec le contrat prêt : état des lieux, signature, et vous roulez vers le Rocher, Ali Mendjeli ou l\'autoroute Est-Ouest en quelques minutes.',
];

$priceRows = [
    ['Citadine', 'Symbol, Clio, i10', '4 500 – 6 000 DA'],
    ['Berline', 'Logan, Sandero, Elantra', '6 000 – 7 500 DA'],
    ['SUV', 'Duster, Stepway, Tucson', '8 500 – 10 000 DA'],
    ['Van 7 places', 'Espace, Expert', '12 000 – 16 000 DA'],
];
$priceNote = 'L\'été et pendant les fêtes religieuses, la diaspora constantinoise fait monter la demande : réservez tôt pour les vols de juillet-août.';

$deliveryText = [
    'Les loueurs constantinois livrent à l\'aéroport Mohamed Boudiaf gratuitement ou avec un supplément modéré indiqué sur l\'annonce. Transmettez votre numéro de vol à la réservation : votre arrivée est suivie en temps réel, même en cas de retard.',
    'La remise se fait à la sortie du terminal ou au parking. Comptez une quinzaine de minutes pour l\'état des lieux et la signature. Pour le retour, convenez de la restitution à l\'aérogare avant votre vol — prévoyez 30 minutes de marge.',
];

$faqs = [
    ['q' => 'Combien coûte une location à l\'aéroport de Constantine ?', 'a' => 'Comptez 4 500 à 6 000 DA/jour pour une citadine, 6 000 à 7 500 DA pour une berline et 8 500 à 10 000 DA pour un SUV. Le supplément livraison aéroport éventuel figure sur l\'annonce.'],
    ['q' => 'Le loueur m\'attend-il en cas de retard de vol ?', 'a' => 'Oui, en communiquant votre numéro de vol (CZL), le loueur suit l\'horaire réel d\'atterrissage et adapte le rendez-vous.'],
    ['q' => 'Peut-on rouler vers Sétif, Mila ou Guelma avec la location ?', 'a' => 'Oui, sauf mention contraire sur l\'annonce. L\'autoroute Est-Ouest est à proximité — vérifiez simplement le kilométrage inclus.'],
    ['q' => 'Accepte-t-on les permis français ou étrangers ?', 'a' => 'Oui, la plupart des loueurs acceptent les permis européens, parfois avec permis international. La condition exacte est sur l\'annonce.'],
    ['q' => 'Peut-on restituer la voiture à l\'aéroport au retour ?', 'a' => 'Oui, la restitution à Mohamed Boudiaf se convient à la réservation. Gardez 30 minutes de marge avant l\'enregistrement pour l\'état des lieux.'],
    ['q' => 'Faut-il verser une caution ?', 'a' => 'Oui, le montant et le mode (espèces ou empreinte) sont précisés sur chaque annonce avant la réservation.'],
];

$nearby = [
    '/location-voiture-constantine' => 'Location voiture Constantine',
    '/location-voiture-setif' => 'Location voiture Sétif',
    '/location-voiture-annaba' => 'Location voiture Annaba',
    '/location-voiture-aeroport-alger' => 'Location aéroport Alger',
];
@endphp

@include('front.pages.partials.aeroport-seo-page')
