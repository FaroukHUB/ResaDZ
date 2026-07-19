<?php
$base            = './';
$cityName        = 'Boumerdès';
$cityParticle    = 'à';
$citySlug        = 'boumerdes';
$cityBreadcrumb  = 'Location voiture Boumerdès';
$cityTagline     = 'Location de voiture à Boumerdès : livraison rapide depuis Alger, assurance incluse, flotte récente.';

$pageTitle       = 'Location voiture Boumerdès — MB CARS DZ';
$pageDescription = 'Louez une voiture à Boumerdès avec MB CARS DZ. Livraison express depuis Rouiba (Alger). SUV, berlines et citadines récents, assurance incluse. Réponse sous 24h.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/location-voiture-boumerdes.php';

$cityIntro = '
<h2 style="font-size:1.2rem;font-weight:700;color:#ffc43a;margin-bottom:12px">Location de voiture à Boumerdès</h2>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Vous habitez ou séjournez à <strong>Boumerdès</strong> et cherchez une <strong>location de voiture</strong> ?
  MB CARS DZ est basée à Rouiba, à quelques kilomètres seulement. Livraison rapide et fiable dans toute la wilaya de Boumerdès :
  Boumerdès centre, Thenia, Boudouaou, Khemis El Khechna, Isser...
</p>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Notre flotte de <strong>SUV, berlines et citadines</strong> récents est disponible avec assurance incluse et prix transparent.
  Proximité géographique garantit une <strong>livraison express</strong> pour vos besoins urgents.
  Idéal pour un week-end, un déplacement professionnel ou un séjour à la côte.
</p>
<p style="color:#ddd;line-height:1.75">
  Boumerdès, à une trentaine de kilomètres à l\'est d\'Alger, bénéficie de superbes côtes et d\'un accès direct à l\'autoroute Est-Ouest.
  Avoir une voiture de location vous permet d\'explorer la région en toute liberté.
</p>
';

$cityFAQs = [
  [
    'q' => 'MB CARS DZ livre-t-il à Boumerdès ?',
    'a' => 'Oui ! MB CARS DZ est basée à Rouiba, très proche de Boumerdès. Nous assurons une livraison rapide dans toute la wilaya de Boumerdès. Contactez-nous pour organiser la livraison.',
  ],
  [
    'q' => 'Combien de temps pour que la voiture arrive à Boumerdès ?',
    'a' => 'Depuis Rouiba, Boumerdès est à environ 20-30 minutes. Nous pouvons souvent livrer le jour même ou le lendemain selon disponibilité. Réservez à l\'avance pour plus de sécurité.',
  ],
  [
    'q' => 'Quels véhicules sont disponibles pour Boumerdès ?',
    'a' => 'Toute la flotte MB CARS DZ : SUV (VW Tiguan, BMW X3, Hyundai Tucson…), berlines et citadines. Tous récents et assurés.',
  ],
  [
    'q' => 'L\'assurance est-elle incluse dans le tarif à Boumerdès ?',
    'a' => 'Oui, l\'assurance est toujours incluse dans le prix affiché. Aucun frais additionnel pour la couverture.',
  ],
  [
    'q' => 'Peut-on louer pour un week-end à Boumerdès ?',
    'a' => 'Absolument ! Nous proposons des locations à la journée, au week-end ou à la semaine. Les tarifs dégressifs rendent les locations de plusieurs jours encore plus avantageuses.',
  ],
  [
    'q' => 'Quels documents sont nécessaires pour louer à Boumerdès ?',
    'a' => 'Pièce d\'identité valide, permis de conduire de plus de 2 ans, et au minimum 25 ans. Pas de carte de crédit obligatoire.',
  ],
  [
    'q' => 'Y a-t-il des frais de livraison pour Boumerdès ?',
    'a' => 'La proximité de Boumerdès (30 min depuis Rouiba) permet des frais de livraison réduits. Contactez-nous pour un devis précis selon votre commune exacte.',
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
        ['@type'=>'ListItem','position'=>2,'name'=>'Location voiture Boumerdès','item'=>'https://mbcarsdzrouiba.com/location-voiture-boumerdes.php'],
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
