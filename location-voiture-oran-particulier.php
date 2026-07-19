<?php
$base            = './';
$cityName        = 'Oran';
$cityParticle    = 'à';
$citySlug        = 'oran-particulier';
$cityBreadcrumb  = 'Location voiture Oran entre particuliers';
$cityTagline     = 'Location de voiture entre particuliers à Oran : livraison sur place, assurance incluse, tarifs transparents.';

$pageTitle       = 'Location voiture Oran entre particuliers — MB CARS DZ';
$pageDescription = 'Louez une voiture à Oran directement entre particuliers avec MB CARS DZ. Livraison à Oran sur demande. Assurance incluse, SUV et berlines récents. Sans frais cachés.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/location-voiture-oran-particulier.php';

$cityIntro = '
<h2 style="font-size:1.2rem;font-weight:700;color:#ffc43a;margin-bottom:12px">Location de voiture entre particuliers à Oran</h2>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Vous êtes à <strong>Oran</strong> et recherchez une <strong>location de voiture entre particuliers</strong> ?
  MB CARS DZ, basée à Rouiba (Alger), assure la <strong>livraison de véhicules à Oran</strong> sur demande.
  SUV, berlines et citadines récents, tous assurés, livrés directement à votre adresse.
</p>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  La location entre particuliers à Oran vous garantit un <strong>contact direct</strong> avec le propriétaire, sans agence
  multinationale. Prix clairs, assurance incluse, réponse sous 24h. Idéal pour un <strong>voyage professionnel ou privé</strong>
  dans la capitale de l\'Ouest algérien.
</p>
<p style="color:#ddd;line-height:1.75">
  Nos véhicules parcourent toute l\'Algérie : de l\'aéroport d\'Alger jusqu\'à Oran, en passant par Sidi Bel Abbès, Tlemcen ou Mostaganem.
  Contactez-nous pour un devis de livraison personnalisé.
</p>
';

$cityFAQs = [
  [
    'q' => 'MB CARS DZ livre-t-il des voitures à Oran ?',
    'a' => 'Oui, MB CARS DZ assure la livraison de véhicules à Oran sur demande. Contactez-nous pour organiser la logistique et obtenir un devis de livraison depuis Alger vers Oran.',
  ],
  [
    'q' => 'Qu\'est-ce que la location entre particuliers à Oran ?',
    'a' => 'C\'est une location directe sans agence multinationale — vous traitez avec le propriétaire du véhicule. MB CARS DZ propose ce service : tarifs compétitifs, flexibilité et contact humain.',
  ],
  [
    'q' => 'Quels véhicules sont disponibles pour Oran ?',
    'a' => 'Toute la flotte MB CARS DZ est accessible : SUV (VW Tiguan, BMW X3, Mercedes GLE…), berlines (Audi A1, VW Golf, BMW Série 1…) et citadines (Fiat 500, Renault Symbol…). Sous réserve de disponibilité.',
  ],
  [
    'q' => 'L\'assurance est-elle incluse pour une location à Oran ?',
    'a' => 'Oui. Tous les véhicules MB CARS DZ sont assurés et le tarif est tout compris. Aucun frais caché.',
  ],
  [
    'q' => 'Quel est le délai pour organiser une location à Oran ?',
    'a' => 'Contactez-nous au moins 48h à l\'avance pour une livraison à Oran. Nous confirmons sous 24h et organisons la livraison selon vos dates.',
  ],
  [
    'q' => 'Quels documents faut-il pour louer à Oran ?',
    'a' => 'Pièce d\'identité valide, permis de conduire de plus de 2 ans, et avoir au minimum 25 ans. Aucune carte de crédit obligatoire.',
  ],
  [
    'q' => 'Quels sont les prix pour une location à Oran ?',
    'a' => 'Les tarifs de base commencent à 8 000 DA/jour pour une citadine. Des frais de livraison depuis Alger peuvent s\'appliquer — contactez-nous pour un devis précis selon votre destination à Oran.',
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
      'telephone'  => '+213656697788',
      'areaServed' => 'DZ',
    ],
    [
      '@type'      => 'BreadcrumbList',
      'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Accueil','item'=>'https://mbcarsdzrouiba.com/'],
        ['@type'=>'ListItem','position'=>2,'name'=>'Location voiture Oran particulier','item'=>'https://mbcarsdzrouiba.com/location-voiture-oran-particulier.php'],
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
