<?php
$base            = './';
$cityName        = 'Sidi Bel Abbès';
$cityParticle    = 'à';
$citySlug        = 'sidi-bel-abbes';
$cityBreadcrumb  = 'Location voiture Sidi Bel Abbès';
$cityTagline     = 'Location de voiture à Sidi Bel Abbès avec MB CARS DZ : livraison sur place, assurance incluse, flotte récente.';

$pageTitle       = 'Location voiture Sidi Bel Abbès — MB CARS DZ';
$pageDescription = 'Louez une voiture à Sidi Bel Abbès avec MB CARS DZ. Livraison à domicile ou en agence. SUV, berlines et citadines récents avec assurance incluse. Tarifs transparents.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/location-voiture-sidi-bel-abbes.php';

$cityIntro = '
<h2 style="font-size:1.2rem;font-weight:700;color:#ffc43a;margin-bottom:12px">Location de voiture à Sidi Bel Abbès</h2>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Besoin d\'une <strong>location de voiture à Sidi Bel Abbès</strong> ? MB CARS DZ assure la livraison de véhicules récents
  dans toute l\'Algérie, dont Sidi Bel Abbès. <strong>SUV, berlines et citadines</strong> assurés, tarifs transparents,
  contact direct sans intermédiaire.
</p>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Que vous soyez un professionnel en déplacement, un particulier pour un événement ou un voyageur de passage,
  MB CARS DZ s\'adapte à vos besoins. <strong>Prix à partir de 8 000 DA / jour</strong> avec tarifs dégressifs.
  Réservez à l\'avance et nous organisez la livraison jusqu\'à votre porte.
</p>
<p style="color:#ddd;line-height:1.75">
  Sidi Bel Abbès, capitale de la wilaya 22, est une ville dynamique du nord-ouest algérien. Disposer d\'un véhicule
  personnel pendant votre séjour vous offre une liberté totale de déplacement vers Oran, Tlemcen ou Mascara.
</p>
';

$cityFAQs = [
  [
    'q' => 'MB CARS DZ livre-t-il des voitures à Sidi Bel Abbès ?',
    'a' => 'Oui, MB CARS DZ livre dans toute l\'Algérie, dont Sidi Bel Abbès. Contactez-nous avec vos dates pour organiser la livraison et recevoir un devis personnalisé.',
  ],
  [
    'q' => 'Quel est le délai pour réserver une voiture à Sidi Bel Abbès ?',
    'a' => 'Nous recommandons de réserver au moins 48h à l\'avance pour permettre l\'organisation de la livraison. Nous confirmons sous 24h.',
  ],
  [
    'q' => 'Quels types de véhicules sont disponibles à Sidi Bel Abbès ?',
    'a' => 'Toute la flotte MB CARS DZ : SUV (VW Tiguan, BMW X3…), berlines (Audi A1, VW Golf…) et citadines (Fiat 500, Renault Symbol…). Sous réserve de disponibilité.',
  ],
  [
    'q' => 'L\'assurance est-elle incluse pour une location à Sidi Bel Abbès ?',
    'a' => 'Oui, l\'assurance est incluse dans le tarif. Pas de frais cachés, prix clairement affiché.',
  ],
  [
    'q' => 'Peut-on louer une voiture sans agence à Sidi Bel Abbès ?',
    'a' => 'Avec MB CARS DZ, vous traitez directement avec les propriétaires — pas d\'agence intermédiaire. C\'est la location entre particuliers : plus flexible, plus abordable.',
  ],
  [
    'q' => 'Quels documents sont nécessaires ?',
    'a' => 'Pièce d\'identité valide, permis de conduire de plus de 2 ans, âge minimum 25 ans. Simple et rapide.',
  ],
  [
    'q' => 'Y a-t-il des frais de livraison supplémentaires ?',
    'a' => 'Des frais de livraison peuvent s\'appliquer pour Sidi Bel Abbès selon la distance. Contactez-nous pour un devis précis incluant la livraison.',
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
        ['@type'=>'ListItem','position'=>2,'name'=>'Location voiture Sidi Bel Abbès','item'=>'https://mbcarsdzrouiba.com/location-voiture-sidi-bel-abbes.php'],
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
