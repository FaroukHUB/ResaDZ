<?php
$base            = './';
$cityName        = 'Sétif';
$cityParticle    = 'à';
$citySlug        = 'setif';
$cityBreadcrumb  = 'Location voiture Sétif';
$cityTagline     = 'Location de voiture à Sétif avec MB CARS DZ : livraison sur place, assurance incluse, flotte récente.';

$pageTitle       = 'Location voiture Sétif — MB CARS DZ';
$pageDescription = 'Louez une voiture à Sétif avec MB CARS DZ. Livraison depuis Alger. SUV, berlines et citadines récents avec assurance incluse. Tarifs dégressifs. Contactez-nous pour un devis.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/location-voiture-setif.php';

$cityIntro = '
<h2 style="font-size:1.2rem;font-weight:700;color:#ffc43a;margin-bottom:12px">Location de voiture à Sétif</h2>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  MB CARS DZ assure la <strong>location de voiture à Sétif</strong> avec livraison dans la wilaya.
  Sétif, capitale des Hauts Plateaux algériens et deuxième pôle économique du pays, est accessible par l\'autoroute
  depuis Alger en environ 2h30. Notre flotte de véhicules récents vous attend.
</p>
<p style="color:#ddd;line-height:1.75;margin-bottom:12px">
  Que vous visitiez les <strong>ruines de Djémila</strong> (site UNESCO), le lac de Boughzoul, ou que vous soyez en
  déplacement professionnel à Sétif, un véhicule personnel vous offre une liberté totale.
  <strong>SUV, berlines et citadines</strong> disponibles avec assurance incluse.
</p>
<p style="color:#ddd;line-height:1.75">
  Réservez avec MB CARS DZ à l\'avance — nous organisons la livraison à Sétif selon vos dates et votre adresse.
  Prix à partir de <strong>8 000 DA / jour</strong>, tarifs dégressifs pour les longues durées.
</p>
';

$cityFAQs = [
  [
    'q' => 'MB CARS DZ livre-t-il des voitures à Sétif ?',
    'a' => 'Oui, MB CARS DZ livre dans toute l\'Algérie, dont Sétif. Contactez-nous avec vos dates et votre adresse exacte pour organiser la livraison et obtenir un devis incluant les frais de transport.',
  ],
  [
    'q' => 'Quel véhicule recommandez-vous pour visiter Djémila et la région de Sétif ?',
    'a' => 'Pour les sites touristiques de Sétif comme Djémila, nous recommandons un SUV ou berline confortables. Nos modèles VW Tiguan, Hyundai Tucson et BMW Série 1 sont très adaptés pour ce type de voyages.',
  ],
  [
    'q' => 'Quels sont les tarifs pour une location à Sétif ?',
    'a' => 'Les tarifs de base commencent à 8 000 DA/jour. Des frais de livraison depuis Alger vers Sétif s\'appliquent — contactez-nous pour un devis complet selon la durée et le modèle souhaité.',
  ],
  [
    'q' => 'L\'assurance est-elle incluse dans le prix ?',
    'a' => 'Oui, l\'assurance est systématiquement incluse dans nos tarifs. Prix affiché = prix final, sans frais cachés.',
  ],
  [
    'q' => 'Comment réserver une voiture pour Sétif ?',
    'a' => 'Envoyez-nous un message WhatsApp ou appelez le +213 656 69 77 88. Indiquez vos dates, le véhicule souhaité et votre adresse à Sétif. Nous confirmons sous 24h.',
  ],
  [
    'q' => 'Quels documents sont nécessaires pour louer à Sétif ?',
    'a' => 'Pièce d\'identité valide, permis de conduire de plus de 2 ans d\'ancienneté, et avoir au minimum 25 ans.',
  ],
  [
    'q' => 'Proposez-vous des tarifs dégressifs pour les longues durées à Sétif ?',
    'a' => 'Oui ! Pour les locations de 3 jours ou plus, nous appliquons des tarifs dégressifs. Plus vous louez longtemps, plus le prix journalier est avantageux. Demandez-nous un devis pour votre durée exacte.',
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
        ['@type'=>'ListItem','position'=>2,'name'=>'Location voiture Sétif','item'=>'https://mbcarsdzrouiba.com/location-voiture-setif.php'],
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
