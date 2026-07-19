<?php
$base            = './';
$cityName        = 'Alger';
$cityParticle    = 'à';
$citySlug        = 'alger-particulier';
$cityBreadcrumb  = 'Location voiture Alger entre particuliers';
$cityTagline     = 'Location de voiture entre particuliers à Alger : contact direct, assurance incluse, prix sans surprise.';

$pageTitle       = 'Location voiture Alger entre particuliers — MB CARS DZ';
$pageDescription = 'Louez une voiture à Alger directement entre particuliers avec MB CARS DZ. Sans agence intermédiaire, assurance incluse, tarifs dégressifs. SUV, berlines, citadines récentes.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/location-voiture-alger-particulier.php';

$cityIntro = '
<h2 style="font-size:1.2rem;font-weight:700;color:#ffc43a;margin-bottom:12px">Location de voiture entre particuliers à Alger</h2>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Vous cherchez à <strong>louer une voiture à Alger entre particuliers</strong> ? MB CARS DZ est une agence à taille humaine,
  gérée directement par ses propriétaires, ce qui signifie un service personnalisé, des tarifs compétitifs et un contact direct sans
  intermédiaire. Pas d\'agence multinationale, pas de frais cachés.
</p>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Basée à <strong>Rouiba, Alger</strong>, notre flotte comprend des <strong>SUV, berlines et citadines</strong> récents, tous assurés.
  Livraison possible à l\'<strong>aéroport Houari Boumediene</strong>, dans votre hôtel ou à domicile.
  Prix à partir de <strong>8 000 DA / jour</strong> avec tarifs dégressifs pour les longues durées.
</p>
<p style="color:#ddd;line-height:1.75">
  La <strong>location de voiture entre particuliers en Algérie</strong> présente de nombreux avantages : flexibilité des conditions,
  négociation directe, et un service qui s\'adapte à vos besoins réels. Appelez-nous ou envoyez un message WhatsApp
  pour discuter de votre projet.
</p>
';

$cityFAQs = [
  [
    'q' => 'Qu\'est-ce que la location de voiture entre particuliers à Alger ?',
    'a' => 'La location entre particuliers à Alger consiste à louer un véhicule directement auprès d\'un propriétaire ou d\'une petite agence — sans passer par les grands réseaux internationaux. MB CARS DZ propose exactement ce service : contact direct, tarifs justes, assurance incluse.',
  ],
  [
    'q' => 'Quels sont les avantages par rapport à une grande agence ?',
    'a' => 'Chez MB CARS DZ : tarifs plus compétitifs, pas de frais cachés, service personnalisé, flexibilité sur les dates et les conditions, et réponse rapide. Vous parlez directement avec le propriétaire du véhicule.',
  ],
  [
    'q' => 'Faut-il une carte de crédit pour louer chez MB CARS DZ à Alger ?',
    'a' => 'Non, aucune carte de crédit n\'est obligatoire. Nous adaptons les conditions de paiement à votre situation. Contactez-nous pour discuter des modalités.',
  ],
  [
    'q' => 'Quels documents sont nécessaires pour louer une voiture à Alger ?',
    'a' => 'Pièce d\'identité valide (carte nationale ou passeport), permis de conduire en cours de validité depuis plus de 2 ans, et avoir au minimum 25 ans. Simple et sans paperasse inutile.',
  ],
  [
    'q' => 'L\'assurance est-elle incluse dans le tarif ?',
    'a' => 'Oui. Tous les véhicules MB CARS DZ sont livrés avec assurance incluse dans le prix affiché. Aucune surprise à la remise.',
  ],
  [
    'q' => 'Proposez-vous la livraison à l\'aéroport d\'Alger ?',
    'a' => 'Oui, nous livrons gratuitement à l\'aéroport Houari Boumediene d\'Alger (Dar El Beïda). Indiquez votre numéro de vol et l\'heure d\'arrivée pour que nous soyons présents à votre descente d\'avion.',
  ],
  [
    'q' => 'Quels sont les prix de location entre particuliers à Alger ?',
    'a' => 'Les tarifs commencent à 8 000 DA/jour pour une citadine, de 20 000 à 45 000 DA/jour pour un SUV. Des tarifs dégressifs s\'appliquent à partir de 3 jours de location.',
  ],
];

$pageSchema = json_encode([
  '@context' => 'https://schema.org',
  '@graph'   => [
    [
      '@type'       => 'CarRental',
      '@id'         => 'https://mbcarsdzrouiba.com/#business',
      'name'        => 'MB CARS DZ',
      'url'         => 'https://mbcarsdzrouiba.com/',
      'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'Rouiba',
        'addressLocality' => 'Alger',
        'addressCountry'  => 'DZ',
      ],
      'telephone'   => '+213656697788',
      'areaServed'  => 'DZ',
    ],
    [
      '@type'      => 'BreadcrumbList',
      'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Accueil','item'=>'https://mbcarsdzrouiba.com/'],
        ['@type'=>'ListItem','position'=>2,'name'=>'Location voiture Alger particulier','item'=>'https://mbcarsdzrouiba.com/location-voiture-alger-particulier.php'],
      ],
    ],
    [
      '@type'      => 'FAQPage',
      'mainEntity' => array_map(fn($faq) => [
        '@type'          => 'Question',
        'name'           => $faq['q'],
        'acceptedAnswer' => ['@type'=>'Answer','text'=> strip_tags($faq['a'])],
      ], $cityFAQs),
    ],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

include __DIR__ . '/partials/header.php';
require __DIR__ . '/data/vehicles.php';
include __DIR__ . '/partials/city-page.php';
