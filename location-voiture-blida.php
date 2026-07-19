<?php
$base            = './';
$cityName        = 'Blida';
$cityParticle    = 'à';
$citySlug        = 'blida';
$cityBreadcrumb  = 'Location voiture Blida';
$cityTagline     = 'Location de voiture à Blida avec MB CARS DZ : assurance incluse, livraison rapide, flotte récente.';

$pageTitle       = 'Location voiture Blida — MB CARS DZ';
$pageDescription = 'Louez une voiture à Blida avec MB CARS DZ. Livraison depuis Alger. SUV, berlines et citadines récentes avec assurance incluse. À partir de 8 000 DA/jour. Réponse sous 24h.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/location-voiture-blida.php';

$cityIntro = '
<h2 style="font-size:1.2rem;font-weight:700;color:#ffc43a;margin-bottom:12px">Location de voiture à Blida</h2>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  <strong>Location de voiture à Blida</strong> avec MB CARS DZ — votre partenaire de confiance pour la mobilité en Algérie.
  Blida, la "Ville des Roses" et capitale de la wilaya 09, est à 45 km au sud-ouest d\'Alger.
  MB CARS DZ livre directement dans la wilaya de Blida : Blida centre, Boufarik, Larbaa, Bougara, Chréa…
</p>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Vous avez besoin d\'un <strong>SUV pour la région montagneuse de Chréa</strong>, d\'une <strong>berline pour un déplacement professionnel</strong>
  ou d\'une <strong>citadine économique</strong> ? Toute notre flotte est disponible à Blida avec assurance incluse et tarifs dégressifs.
</p>
<p style="color:#ddd;line-height:1.75">
  La location de voiture à Blida vous donne accès au Parc national de Chréa, aux orangeraies, au Mitidja et à
  l\'aéroport d\'Alger en 30 minutes. Réservez avec MB CARS DZ et nous livrons à votre porte.
</p>
';

$cityFAQs = [
  [
    'q' => 'MB CARS DZ livre-t-il des voitures à Blida ?',
    'a' => 'Oui, MB CARS DZ livre dans toute la wilaya de Blida. Avec Rouiba comme base, Blida est à environ 50 minutes. Contactez-nous pour organiser la livraison selon vos dates.',
  ],
  [
    'q' => 'Quel véhicule recommandez-vous pour visiter la région de Blida ?',
    'a' => 'Pour Chréa et les zones montagneuses, nous recommandons un SUV (VW Tiguan, Hyundai Tucson, BMW X3). Pour Blida centre et la plaine, une berline ou citadine convient parfaitement.',
  ],
  [
    'q' => 'Quels sont les tarifs de location à Blida ?',
    'a' => 'Les prix commencent à 8 000 DA/jour pour une citadine, entre 20 000 et 45 000 DA pour un SUV. Des frais de livraison à Blida peuvent s\'ajouter selon votre commune — contactez-nous pour un devis.',
  ],
  [
    'q' => 'L\'assurance est-elle incluse ?',
    'a' => 'Oui, l\'assurance est incluse dans tous nos tarifs. Aucune surprise à la remise du véhicule.',
  ],
  [
    'q' => 'Comment réserver une voiture pour Blida ?',
    'a' => 'Contactez-nous via WhatsApp (+213 656 69 77 88) ou par téléphone avec vos dates, le modèle souhaité et votre adresse à Blida. Confirmation sous 24h.',
  ],
  [
    'q' => 'Quels documents faut-il pour louer à Blida ?',
    'a' => 'Pièce d\'identité valide, permis de conduire en cours de validité de plus de 2 ans, et avoir au minimum 25 ans.',
  ],
  [
    'q' => 'Peut-on louer pour aller à l\'aéroport depuis Blida ?',
    'a' => 'Oui ! Si vous avez besoin d\'un véhicule pour partir depuis Blida vers l\'aéroport Houari Boumediene, MB CARS DZ peut organiser la livraison et la reprise du véhicule à l\'aéroport.',
  ],
];

$pageSchema = json_encode([
  '@context' => 'https://schema.org',
  '@graph'   => [
    [
      '@type'      => 'CarRental',
      '@id'        => 'https://mbcarsdzrouiba.com/#business',
      'name'       => 'MB CARS DZ',
      'url'        => 'https://mbcarsdzrouiba.com/',
      'address'    => ['@type'=>'PostalAddress','streetAddress'=>'Rouiba','addressLocality'=>'Alger','addressCountry'=>'DZ'],
      'telephone'  => '+213656697788',
      'areaServed' => 'DZ',
    ],
    [
      '@type'      => 'BreadcrumbList',
      'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Accueil','item'=>'https://mbcarsdzrouiba.com/'],
        ['@type'=>'ListItem','position'=>2,'name'=>'Location voiture Blida','item'=>'https://mbcarsdzrouiba.com/location-voiture-blida.php'],
      ],
    ],
    [
      '@type'      => 'FAQPage',
      'mainEntity' => array_map(fn($faq) => [
        '@type'=>'Question','name'=>$faq['q'],
        'acceptedAnswer'=>['@type'=>'Answer','text'=>strip_tags($faq['a'])],
      ], $cityFAQs),
    ],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

include __DIR__ . '/partials/header.php';
require __DIR__ . '/data/vehicles.php';
include __DIR__ . '/partials/city-page.php';
