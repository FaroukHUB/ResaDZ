<?php
$base             = './';
$pageTitle        = 'Location de voiture à Alger & Algérie | MB CARS DZ';
$pageDescription  = "Louez une voiture à Alger chez MB CARS DZ : SUV, berlines et citadines récentes avec assurance incluse. Livraison aéroport Houari Boumediene. Entre particuliers. À partir de 8 000 DA/jour.";
$canonicalUrl     = 'https://mbcarsdzrouiba.com/';
$pageSchema       = json_encode([
  '@context' => 'https://schema.org',
  '@graph'   => [
    [
      '@type'       => 'CarRental',
      '@id'         => 'https://mbcarsdzrouiba.com/#business',
      'name'        => 'MB CARS DZ',
      'url'         => 'https://mbcarsdzrouiba.com/',
      'logo'        => 'https://mbcarsdzrouiba.com/assets/logo.jpeg',
      'image'       => 'https://mbcarsdzrouiba.com/assets/hero.webp',
      'description' => "Location de voitures à Rouiba, Alger. SUV, berlines et citadines récents avec assurance incluse et livraison à l'aéroport d'Alger.",
      'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'Rouiba',
        'addressLocality' => 'Alger',
        'addressRegion'   => 'Alger',
        'addressCountry'  => 'DZ',
      ],
      'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => 36.7413406,
        'longitude' => 3.2835529,
      ],
      'telephone' => '+213656697788',
      'openingHoursSpecification' => [
        '@type'       => 'OpeningHoursSpecification',
        'dayOfWeek'   => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
        'opens'       => '00:00',
        'closes'      => '23:59',
      ],
      'priceRange' => '8000 DA – 60 000 DA / jour',
      'areaServed' => 'DZ',
      'sameAs'     => [
        'https://www.facebook.com/MBcarsdzrouiba',
        'https://www.instagram.com/mbcarsdzrouiba',
      ],
    ],
    [
      '@type'        => 'FAQPage',
      'mainEntity'   => [
        [
          '@type' => 'Question',
          'name'  => 'Quels types de voitures sont disponibles à Alger chez MB CARS DZ ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'MB CARS DZ propose des SUV (Mercedes GLE, VW Tiguan, BMW X3…), des berlines (Audi A1, BMW Série 1, VW Golf…) et des citadines (Fiat 500, Renault Symbol, Dacia Stepway…), tous récents et livrés avec assurance incluse.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'Quel est le prix de location de voiture à Alger ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Les tarifs commencent à 8 000 DA par jour pour une citadine, jusqu\'à 60 000 DA par jour pour un SUV premium. Des prix dégressifs s\'appliquent pour les locations de longue durée.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'L\'assurance est-elle incluse dans le prix ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Oui, tous les véhicules MB CARS DZ sont assurés et le coût est inclus dans le tarif affiché. Aucun frais caché.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'Livrez-vous à l\'aéroport d\'Alger ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Oui, MB CARS DZ propose la livraison gratuite à l\'aéroport Houari Boumediene d\'Alger (Dar El Beïda). Précisez votre vol et heure d\'arrivée lors de votre demande.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'Peut-on louer une voiture entre particuliers en Algérie ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'MB CARS DZ fonctionne comme une location directe entre particuliers : pas d\'agence multinationale, service personnalisé, prix compétitifs et contact direct avec le propriétaire.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'Livrez-vous en dehors d\'Alger ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Oui, MB CARS DZ livre partout en Algérie sur demande : Blida, Boumerdès, Oran, Sétif, Sidi Bel Abbès et plus. Contactez-nous pour un devis de livraison.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'Quel est le délai de confirmation d\'une réservation ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Nous confirmons votre demande sous 24 heures. Si le véhicule choisi n\'est pas disponible, nous vous proposons une alternative adaptée.'],
        ],
        [
          '@type' => 'Question',
          'name'  => 'Quels documents faut-il pour louer une voiture en Algérie ?',
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Il faut une pièce d\'identité valide (carte nationale ou passeport), un permis de conduire valide depuis plus de 2 ans, et avoir au minimum 25 ans.'],
        ],
      ],
    ],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

include __DIR__ . '/partials/header.php';

// On charge la liste des véhicules
require __DIR__ . '/data/vehicles.php';

// On prépare quelques structures utiles
$vehiclesByCategory = [
    'suv'      => [],
    'berline'  => [],
    'citadine' => [],
];

foreach ($vehicles as $v) {
    if (!empty($v['category']) && isset($vehiclesByCategory[$v['category']])) {
        $vehiclesByCategory[$v['category']][] = $v;
    }
}

// ===============================
// Notre sélection (max 5 cartes)
// ===============================
$selectionOrder = [
    'Mercedes GLE 2024 Full Option',
    'VW Tiguan 2024 à 2025', // même texte que dans data/vehicles.php
    'BMW Série 1 2025',
    'Audi A1 2023',
];

$selection = [];
if (!empty($vehiclesByCategory['suv'])) {
    $usedIndexes = [];

    // 1) On essaie de récupérer les modèles dans l’ordre voulu
    foreach ($selectionOrder as $name) {
        foreach ($vehiclesByCategory['suv'] as $idx => $v) {
            if (!isset($v['full_name'])) continue;
            if ($v['full_name'] === $name) {
                $selection[]       = $v;
                $usedIndexes[$idx] = true;
                break;
            }
        }
    }

    // 2) On complète jusqu’à 5 cartes avec d’autres SUV
    if (count($selection) < 5) {
        foreach ($vehiclesByCategory['suv'] as $idx => $v) {
            if (isset($usedIndexes[$idx])) continue;
            $selection[] = $v;
            if (count($selection) === 5) break;
        }
    }
}



// Fonction d’affichage d’une carte véhicule en gardant EXACTEMENT le design
function render_vehicle_card(array $v): void
{
    $fullName = htmlspecialchars($v['full_name'], ENT_QUOTES, 'UTF-8');
    $brand    = htmlspecialchars($v['brand'], ENT_QUOTES, 'UTF-8');
    $model    = htmlspecialchars($v['model'], ENT_QUOTES, 'UTF-8');
    $price    = htmlspecialchars($v['price'], ENT_QUOTES, 'UTF-8');
    $image    = htmlspecialchars($v['image'], ENT_QUOTES, 'UTF-8');
    $logo     = htmlspecialchars($v['logo'], ENT_QUOTES, 'UTF-8');

    // Certaines cartes ont la note *Photos non contractuelles* dans ton HTML
    $withNoteNames = [
        'Fiat 500X 2024',
        'Fiat Doblo',
        'Skoda Kamiq',
        'Seat Ateca',
        'Renault Symbol',
        'Dacia Stepway',
        'Fiat 500',
        'Fiat Tipo',
    ];
    $hasNote = in_array($v['full_name'], $withNoteNames, true);
    ?>
    <article class="card-veh"
             data-title="<?php echo $fullName; ?>"
             data-brand="<?php echo strtolower($brand); ?>">
      <div class="head">
        <img class="brand-logo"
             src="<?php echo $logo; ?>"
             alt="<?php echo $brand; ?>"
             loading="lazy">
        <h4 class="model"><span><?php echo $brand; ?></span> <?php echo $model; ?></h4>
      </div>

      <img class="photo"
           src="<?php echo $image; ?>"
           alt="<?php echo $fullName; ?>"
           loading="lazy">

      <?php if ($hasNote): ?>
        <p class="card-note">
          *Photos non contractuelles&nbsp;: modèle, année ou couleur peuvent varier selon disponibilité.
        </p>
      <?php endif; ?>

      <a href="/conditions.php" class="btn-conditions">
        Voir nos conditions
      </a>

      <div class="body">
        <div class="price"><?php echo $price; ?> <small>Prix / jour</small></div>
        <div class="badges">
          <span class="pill">✅ Assurance incluse</span>
          <span class="pill">✈️ Livraison offerte à l’aéroport d’Alger</span>
          <span class="pill">⤵️ Prix dégressif selon la durée</span>
        </div>
      </div>

      <div class="actions">
        <a class="btn btn-call" href="tel:+213656697788" data-i18n="call">Nous appeler</a>
        <!-- On ajoute la classe btn-ask pour que ton script modal s’applique correctement -->
        <button class="btn btn-gold btn-ask" data-ask-car="<?php echo $fullName; ?>">Faire une demande</button>
      </div>
    </article>
    <?php
}
?>

<?php
require_once __DIR__ . '/config-lang.php';
?>


<body>
<!-- Google Translate (caché) -->
<div id="google_translate_element" style="display:none"></div>


<section class="hero-carousel-container">

  <div class="carousel-wrapper">

    <!-- Slide 1 -->
    <div class="carousel-slide active">
      <img src="assets/hero.webp" alt="hero" class="hero-bg" />
      <div class="hero-overlay"></div>

      <div class="hero-caption">
        <h1 class="hero-title glow">Location de voiture à Alger<br><span style="font-size:.75em;opacity:.85">Roulez avec style.</span></h1>
        <a href="#airportForm" class="btn btn-gold hero-cta">Faire une demande</a>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="carousel-slide">
      <img src="assets/hero2.webp" alt="..." class="hero-bg" />
      <div class="hero-overlay"></div>

      <div class="hero-caption">
        <h1 class="hero-title glow">Entre mer et montagne, <br>votre road trip vous attend...</h1>
        <a href="#airportForm" class="btn btn-gold hero-cta">Faire une demande</a>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="carousel-slide">
      <img src="assets/hero3.webp" alt="..." class="hero-bg" />
      <div class="hero-overlay"></div>

      <div class="hero-caption">
        <h1 class="hero-title glow">Vivez l'incroyable. <br>Louez votre liberté. ...</h1>
        <a href="#airportForm" class="btn btn-gold hero-cta">Faire une demande</a>
      </div>
    </div>

</section>

<!-- INTRO SEO -->
<section class="seo-intro wrap" id="intro" style="padding:40px 0 24px;max-width:900px;margin:0 auto">
  <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:12px;color:#ffc43a">Location de voiture à Alger — MB CARS DZ</h2>
  <p style="line-height:1.75;color:#ddd;margin-bottom:10px">
    Besoin de <strong>louer une voiture à Alger</strong> ou en Algérie ? MB CARS DZ met à votre disposition une flotte de véhicules récents — SUV, berlines et citadines — avec <strong>assurance incluse</strong> et <strong>livraison à l'aéroport d'Alger</strong> ou à votre domicile.
  </p>
  <p style="line-height:1.75;color:#ddd;margin-bottom:10px">
    Basée à <strong>Rouiba, Alger</strong>, notre agence propose une <strong>location de voiture entre particuliers</strong> : contact direct, sans intermédiaire, tarifs transparents à partir de <strong>8 000 DA / jour</strong>. Nous intervenons également à <a href="/location-voiture-blida.php" style="color:#ffc43a">Blida</a>, <a href="/location-voiture-boumerdes.php" style="color:#ffc43a">Boumerdès</a>, <a href="/location-voiture-oran-particulier.php" style="color:#ffc43a">Oran</a>, <a href="/location-voiture-setif.php" style="color:#ffc43a">Sétif</a> et <a href="/location-voiture-sidi-bel-abbes.php" style="color:#ffc43a">Sidi Bel Abbès</a>.
  </p>
</section>

<!-- SECTION VEHICLES -->
<section class="vehicles wrap" id="vehicles">

  <!-- Bloc 1 : Notre sélection -->
  <div class="section-block">
    <div class="sec-head" id="head-1" data-target="#row-1">
      <button class="sec-arrow left mobile-only" aria-label="Précédent">‹</button>
      <div>
        <p class="sec-kicker" data-i18n="ourSelection">Notre sélection</p>
        <h2 class="sec-title auto-effect" data-i18n="ourSelection">Notre sélection</h2>
      </div>
      <button class="sec-arrow right mobile-only" aria-label="Suivant">›</button>
    </div>

    <div class="veh-row" id="row-1">
      <button class="nav-arrow left" aria-label="Précédent">‹</button>
      <div class="cards-track">
        <?php if (!empty($selection)): ?>
          <?php foreach ($selection as $v): ?>
            <?php render_vehicle_card($v); ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <button class="nav-arrow right" aria-label="Suivant">›</button>
    </div>
  </div>

  <!-- Bloc 2 : SUV -->
  <div class="section-block">
    <div class="sec-head" id="head-4" data-target="#row-4">
      <button class="sec-arrow left mobile-only" aria-label="Précédent">‹</button>
      <div>
        <p class="sec-kicker" data-i18n="high">suv</p>
        <h2 class="sec-title auto-effect" data-i18n="high">SUV</h2>
      </div>
      <button class="sec-arrow right mobile-only" aria-label="Suivant">›</button>
    </div>

    <div class="veh-row" id="row-4">
      <button class="nav-arrow left" aria-label="Précédent">‹</button>
      <div class="cards-track">
        <?php if (!empty($vehiclesByCategory['suv'])): ?>
          <?php foreach (array_slice($vehiclesByCategory['suv'], 0, 5) as $v): ?>
            <?php render_vehicle_card($v); ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <button class="nav-arrow right" aria-label="Suivant">›</button>
    </div>
  </div>

  <div class="sec-cta">
    <a class="btn-gold-rect" href="/suv.php">Voir la gamme compléte</a>
  </div>

  <!-- Bloc 3 : BERLINE -->
  <div class="section-block">
    <div class="sec-head" id="head-3" data-target="#row-3">
      <button class="sec-arrow left mobile-only" aria-label="Précédent">‹</button>
      <div>
        <p class="sec-kicker" data-i18n="mid">berline</p>
        <h2 class="sec-title auto-effect" data-i18n="mid">BERLINE</h2>
      </div>
      <button class="sec-arrow right mobile-only" aria-label="Suivant">›</button>
    </div>

    <div class="veh-row" id="row-3">
      <button class="nav-arrow left" aria-label="Précédent">‹</button>
      <div class="cards-track">
        <?php if (!empty($vehiclesByCategory['berline'])): ?>
          <?php foreach (array_slice($vehiclesByCategory['berline'], 0, 5) as $v): ?>
            <?php render_vehicle_card($v); ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <button class="nav-arrow right" aria-label="Suivant">›</button>
    </div>
  </div>

  <div class="sec-cta">
    <a class="btn-gold-rect" href="/berlines.php">Voir la gamme compléte</a>
  </div>

  <!-- Bloc 4 : CITADINE -->
  <div class="section-block">
    <div class="sec-head" id="head-2" data-target="#row-2">
      <button class="sec-arrow left mobile-only" aria-label="Précédent">‹</button>
      <div>
        <p class="sec-kicker" data-i18n="entry">citadine</p>
        <h2 class="sec-title auto-effect" data-i18n="entry">CITADINE</h2>
      </div>
      <button class="sec-arrow right mobile-only" aria-label="Suivant">›</button>
    </div>

    <div class="veh-row" id="row-2">
      <button class="nav-arrow left" aria-label="Précédent">‹</button>
      <div class="cards-track">
        <?php if (!empty($vehiclesByCategory['citadine'])): ?>
          <?php foreach (array_slice($vehiclesByCategory['citadine'], 0, 5) as $v): ?>
            <?php render_vehicle_card($v); ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <button class="nav-arrow right" aria-label="Suivant">›</button>
    </div>
  </div>

  <div class="sec-cta">
    <a class="btn-gold-rect" href="/citadines.php">Voir la gamme compléte</a>
  </div>

</section>


<section class="steps wrap" id="how-it-works" aria-labelledby="steps-title">
  <header class="steps-head">
    <p class="steps-kicker">Simple • Rapide • Fiable</p>
    <h2 class="steps-title" id="steps-title">Louez en 3 étapes</h2>
  </header>

  <ol class="steps-grid">
    <!-- ÉTAPE 1 -->
    <li class="step-card">
      <div class="step-top">
        <div class="step-icon" aria-hidden="true">
          <!-- Car -->
          <svg viewBox="0 0 24 24" class="i-40"><path fill="currentColor" d="M5 11l1.7-4.3A2 2 0 0 1 8.55 5h6.9a2 2 0 0 1 1.85 1.7L19 11h1a2 2 0 0 1 2 2v3.5a1.5 1.5 0 0 1-3 0V16H5v.5a1.5 1.5 0 0 1-3 0V13a2 2 0 0 1 2-2h1Zm3.6-4a1 1 0 0 0-.93.66L6.9 11h10.2l-.77-2.34a1 1 0 0 0-.94-.66H8.6ZM7.5 17.5a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Zm9 0a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z"/></svg>
        </div>
        <div class="step-badge">1</div>
      </div>
      <h3 class="step-title">Choisissez votre véhicule</h3>
      <p class="step-text">Sélectionnez le modèle, vos dates, si vous souhaitez ou non être livré à l'aéroport ou ailleur.</p>
      <div class="step-actions"></div>
    </li>

    <!-- ÉTAPE 2 -->
    <li class="step-card">
      <div class="step-top">
        <div class="step-icon" aria-hidden="true">
          <!-- Envelope -->
          <svg viewBox="0 0 24 24" class="i-40"><path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/></svg>
        </div>
        <div class="step-badge">2</div>
      </div>
      <h3 class="step-title">Nous traitons votre demande</h3>
      <p class="step-text">Nous vérifions la disponibilité du véhicule aux dates chosisis et nous préparons votre dossier.</p>
      <div class="step-actions"></div>
    </li>

    <!-- ÉTAPE 3 -->
    <li class="step-card">
      <div class="step-top">
        <div class="step-icon" aria-hidden="true">
          <!-- Clock -->
          <svg viewBox="0 0 24 24" class="i-40"><path fill="currentColor" d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2Zm1 5v5.05l4.2 2.43-.75 1.3L11 13V7h2Z"/></svg>
        </div>
        <div class="step-badge">3</div>
      </div>
      <h3 class="step-title">Confirmation sous 24&nbsp;h</h3>
      <p class="step-text">Nous vous rappelons dans un delai de 24h maximum. Si le véhicule choisit n'est pas dispo, nous vous proposons une alternative.</p>
      <div class="step-actions two"></div>
    </li>
  </ol>

  <footer class="steps-bottom">
    Besoin urgent ?
    <a href="tel:+213656697788" class="steps-phone">+213&nbsp;656&nbsp;69&nbsp;77&nbsp;88</a>
    <a href="tel:+213560989543" class="steps-phone">+213&nbsp;560&nbsp;98&nbsp;95&nbsp;43</a>
    <a href="tel:+213560989541" class="steps-phone">+213&nbsp;560&nbsp;98&nbsp;95&nbsp;41</a>
    <a href="tel:+213560989483" class="steps-phone">+213&nbsp;560&nbsp;98&nbsp;94&nbsp;83</a>
  </footer>
</section>

<!-- app.js -->
<script src="app.js" defer></script>


<!-- ===== FAQ Section ===== -->
<section class="faq-section wrap" id="faq" style="padding:48px 0;max-width:900px;margin:0 auto">
  <header style="text-align:center;margin-bottom:32px">
    <p class="steps-kicker">Questions fréquentes</p>
    <h2 style="font-size:1.6rem;font-weight:700;color:#ffc43a">Location de voiture à Alger — FAQ</h2>
  </header>

  <div class="faq-list" style="display:flex;flex-direction:column;gap:0">

    <details class="faq-item" open>
      <summary class="faq-q"><h3>Quels types de voitures proposez-vous à Alger ?</h3></summary>
      <div class="faq-a">
        <p>MB CARS DZ dispose d'une gamme complète : <strong>SUV</strong> (Mercedes GLE, VW Tiguan, BMW X3, Hyundai Tucson…), <strong>berlines</strong> (Audi A1, BMW Série 1, VW Golf, Peugeot 208…) et <strong>citadines</strong> (Fiat 500, Renault Symbol, Dacia Stepway…). Tous les véhicules sont récents, entretien suivi, assurance incluse.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>Quel est le prix de location de voiture à Alger ?</h3></summary>
      <div class="faq-a">
        <p>Les tarifs démarrent à <strong>8 000 DA/jour</strong> pour une citadine, entre <strong>20 000 et 45 000 DA/jour</strong> pour un SUV selon le modèle. Des <strong>tarifs dégressifs</strong> sont appliqués pour les locations de plus de 3 jours.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>L'assurance est-elle incluse ?</h3></summary>
      <div class="faq-a">
        <p>Oui. L'assurance est incluse dans le prix affiché sur chaque véhicule. Aucun frais caché, aucune surprise à la remise du véhicule.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>Livrez-vous à l'aéroport d'Alger ?</h3></summary>
      <div class="faq-a">
        <p>Oui, MB CARS DZ propose la <strong>livraison gratuite à l'aéroport Houari Boumediene</strong> d'Alger (Dar El Beïda). Il suffit d'indiquer votre numéro de vol et l'heure d'arrivée lors de votre demande.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>Comment fonctionne la location entre particuliers ?</h3></summary>
      <div class="faq-a">
        <p>MB CARS DZ est une agence à taille humaine — vous traitez directement avec nous, sans intermédiaire. C'est le principe de la <strong>location de voiture entre particuliers en Algérie</strong> : prix justes, contact direct, service personnalisé. Disponible à <a href="/location-voiture-alger-particulier.php">Alger</a> et <a href="/location-voiture-oran-particulier.php">Oran</a>.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>Livrez-vous en dehors d'Alger ?</h3></summary>
      <div class="faq-a">
        <p>Oui, nous livrons partout en Algérie sur demande : <a href="/location-voiture-blida.php">Blida</a>, <a href="/location-voiture-boumerdes.php">Boumerdès</a>, <a href="/location-voiture-oran-particulier.php">Oran</a>, <a href="/location-voiture-setif.php">Sétif</a>, <a href="/location-voiture-sidi-bel-abbes.php">Sidi Bel Abbès</a>. Contactez-nous pour un devis.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>Quels documents faut-il pour louer ?</h3></summary>
      <div class="faq-a">
        <p>Pièce d'identité valide (carte nationale ou passeport), permis de conduire valide depuis plus de <strong>2 ans</strong>, et avoir au minimum <strong>25 ans</strong>. Aucune carte de crédit obligatoire.</p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-q"><h3>Sous quel délai confirmez-vous une réservation ?</h3></summary>
      <div class="faq-a">
        <p>Nous confirmons votre réservation <strong>sous 24 heures</strong>. Si le véhicule choisi n'est pas disponible, nous vous proposons une alternative équivalente.</p>
      </div>
    </details>

  </div>
</section>

<style>
.faq-item{border-bottom:1px solid #2a2a2a}
.faq-item:first-child{border-top:1px solid #2a2a2a}
.faq-q{
  list-style:none;padding:18px 4px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;
  font-weight:600;font-size:1rem;color:#fff;gap:12px
}
.faq-q h3{margin:0;font-size:inherit;font-weight:inherit;color:inherit}
.faq-q::after{content:'+';font-size:1.4rem;color:#ffc43a;flex-shrink:0;transition:transform .2s}
details[open] .faq-q::after{transform:rotate(45deg)}
.faq-a{padding:0 4px 18px;color:#ccc;line-height:1.7}
.faq-a p{margin:0}
.faq-a a{color:#ffc43a}
</style>

<!-- ===== Section Réservation Aéroport ===== -->
<section class="airport-rent wrap" id="airport-rent">

  <!-- Hero style "bande image" modernisé -->
  <div class="airport-hero hero-style">
    <img src="assets/vip.webp" alt="Service VIP aéroport" class="hero-bg" />
    <div class="hero-overlay"></div>

    <div class="hero-caption">
      <h2 class="hero-title">Besoin d'une voiture à votre arrivée&nbsp;?</h2>
    </div>
  </div>

  <!-- Formulaire -->
  <form class="ask-form" id="airportForm" novalidate>
    <div class="grid">
      <div>
        <label class="lbl">Nom complet</label>
        <input type="text" name="name" placeholder="Votre nom" required>
      </div>

      <div>
        <label class="lbl">Téléphone</label>
        <input type="tel" name="phone" placeholder="+213 ..." required>
      </div>

      <div>
        <label class="lbl">E-mail</label>
        <input type="email" name="email" placeholder="vous@email.com" required>
      </div>

      <div>
        <label class="lbl">Date d'arrivée</label>
        <input type="date" name="date" required>
      </div>

      <div>
        <label class="lbl">Heure</label>
        <select name="time" id="airportTime" required></select>
      </div>

      <div>
        <label class="lbl">Vol / Terminal (optionnel)</label>
        <input type="text" name="flight" placeholder="Ex: EK758, Terminal Ouest">
      </div>

      <div>
        <label class="lbl">Modèle souhaité</label>
        <select name="car" required>
          <option value="" selected disabled>Choisir un véhicule</option>
          <?php foreach ($vehicles as $v): ?>
            <option value="<?php echo htmlspecialchars($v['full_name'], ENT_QUOTES, 'UTF-8'); ?>">
              <?php echo htmlspecialchars($v['full_name'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="full">
        <label class="lbl">Lieu de remise</label>
        <input type="text" name="place" placeholder="Hall Arrivées Aéroport d'Alger ou adresse précise" required>
      </div>

      <div class="full">
        <label class="lbl">Message (optionnel)</label>
        <textarea name="msg" rows="3" placeholder="Siège bébé, chauffeur, etc."></textarea>
      </div>
    </div>

    <div class="actions actions--stack">
      <button type="button" id="sendMail" class="btn-gold-rect">Envoyer</button>
      <a id="waLink" class="btn btn-wa" target="_blank" rel="noopener">Envoyer sur WhatsApp</a>
    </div>
  </form>
</section>

<script>
  function initGoogleTranslate() {
    new google.translate.TranslateElement({
      pageLanguage: 'fr',
      includedLanguages: 'fr,en,ar',
      autoDisplay: false
    }, 'google_translate_element');
  }

  function withGCombo(cb) {
    const tryGet = () => {
      const combo = document.querySelector('.goog-te-combo');
      if (combo) cb(combo);
      else setTimeout(tryGet, 120);
    };
    tryGet();
  }

  function setLang(code) {
    withGCombo((combo) => {
      if (combo.value !== code) {
        combo.value = code;
        combo.dispatchEvent(new Event('change'));
      }
      document.documentElement.dir = (code === 'ar') ? 'rtl' : 'ltr';
      document.body.classList.toggle('rtl', code === 'ar');
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const langSelect = document.getElementById('langSelect');
    if (langSelect) {
      langSelect.addEventListener('change', e => setLang(e.target.value));
    }

    const dropLangMobile = document.getElementById('langDropMobile');
    if (dropLangMobile) {
      dropLangMobile.addEventListener('click', (e) => {
        const li = e.target.closest('li[data-lang]');
        if (!li) return;
        setLang(li.dataset.lang);
      });
    }

    const saved = localStorage.getItem('lang');
    if (saved) setLang(saved);
  });
</script>

<script src="//translate.google.com/translate_a/element.js?cb=initGoogleTranslate"></script>

<style>
.mobile-panel .mp-inner .contact-suite{display:flex;flex-direction:column;gap:14px;margin-top:14px}
.mobile-panel .mp-inner .contact-suite .contact-item{
  display:flex;flex-direction:column;gap:6px;background:#f7f7f7;border-radius:12px;padding:12px 14px
}
.mobile-panel .mp-inner .contact-suite .contact-item span{font-weight:700;color:#111;font-size:15px}
.mobile-panel .mp-inner .contact-suite .contact-item .contact-btns{display:flex;gap:8px}
.mobile-panel .mp-inner .contact-suite .contact-item .btn-call-mini,
.mobile-panel .mp-inner .contact-suite .contact-item .btn-wa-mini{
  flex:1;text-align:center;padding:8px 0;border-radius:999px;font-weight:700;font-size:14px
}
.mobile-panel .mp-inner .contact-suite .contact-item .btn-call-mini{background:#111;color:#fff}
.mobile-panel .mp-inner .contact-suite .contact-item .btn-wa-mini{background:#25D366;color:#fff}

.mobile-panel .mp-inner .mp-nav li a{
  display:block;padding:14px 8px;font-size:16px;font-weight:700;border-bottom:1px solid #eee
}
.mobile-panel .mp-inner .mp-nav li a:hover{background:#fafafa}
</style>

<section class="about-and-values wrap" id="about">
  <section id="apropos" class="about-section wrap">
    <div class="about-text">
      <h2 class="sec-title auto-effect">MBCars DZ, bien plus qu’une simple location</h2>
      <p>
        Situés à <strong>Rouiba</strong>, nous accompagnons chaque voyageur, famille ou professionnel,
        avec une flotte de véhicules <strong>entre entrée de gamme et haut de gamme</strong>.
        Notre objectif est simple : que votre séjour se passe <strong>avec confort, tranquillité et sérénité</strong>.
        Livraison possible <strong>partout en Algérie</strong>, véhicules <strong>assurés</strong> & entretien suivi.
      </p>
    </div>

    <div class="values-grid">

      <div class="value-box">
        <div class="icon">🚗</div>
        <h3>Véhicules récents</h3>
        <p>Entretien rigoureux & assurance incluse.</p>
      </div>

      <div class="value-box">
        <div class="icon">📍</div>
        <h3>Livraison partout</h3>
        <p>Aéroport, hôtel, domicile — c’est vous qui choisissez.</p>
      </div>

      <div class="value-box">
        <div class="icon">🤝</div>
        <h3>Service humain</h3>
        <p>On répond. On trouve une solution. Toujours.</p>
      </div>

      <div class="value-box">
        <div class="icon">💳</div>
        <h3>Prix clair</h3>
        <p>Pas de surprise, pas de frais cachés.</p>
      </div>
    </div>

    <div class="cta-zone">
      <a href="#airport-rent" class="btn-gold-rect big">Réserver maintenant</a>
    </div>

  </section>
</section>

<style>
  .map-contact-section{
    margin:40px 0;
    padding:32px 20px;
    background:#000;
    border:1px solid #d4af37;
    border-radius:18px;
    box-shadow:0 18px 50px rgba(0,0,0,.7);
  }
  .map-contact-title{
    text-align:center;
    font-size:1.6rem;
    margin:0 0 24px;
    color:#fff;
    font-weight:700;
  }
  .map-contact-highlight{color:#ffc43a;}

  .map-contact-grid{
    display:flex;
    flex-wrap:wrap;
    gap:24px;
    align-items:stretch;
  }
  .map-contact-left,
  .map-contact-right{
    flex:1 1 280px;
  }

  .map-contact-map{
    background:#2b2b2b;
    padding:8px;
    border-radius:14px;
    overflow:hidden;
  }
  .map-contact-map iframe{
    display:block;
    width:100%;
    height:260px;
    border:0;
    border-radius:10px;
  }

  .map-contact-separator{
    width:2px;
    background:linear-gradient(to bottom,#d4af37,#ffc43a,#d4af37);
    border-radius:999px;
    opacity:.9;
  }

  .map-contact-label{
    color:#ffc43a;
    font-weight:600;
    font-size:.9rem;
    margin:0 0 6px;
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  .map-contact-address{
    color:#f5f5f5;
    margin:0 0 10px;
    font-size:.95rem;
    line-height:1.5;
  }
  .map-contact-hours{
    color:#bbbbbb;
    font-size:.9rem;
    margin:0 0 18px;
  }

  .map-contact-button{
    display:inline-block;
    background:#ffc43a;
    color:#000;
    padding:10px 24px;
    border-radius:999px;
    font-size:1rem;
    font-weight:700;
    text-decoration:none;
    transition:0.25s;
  }
  .map-contact-button:hover{
    filter:brightness(1.05);
  }

  .map-contact-note{
    color:#888;
    font-size:.85rem;
    margin-top:10px;
  }

  @media (max-width:768px){
    .map-contact-section{
      padding:24px 16px;
    }
    .map-contact-grid{
      flex-direction:column;
    }
    .map-contact-separator{
      display:none;
    }
    .map-contact-map iframe{
      height:220px;
    }
  }
</style>

<section class="map-contact-section" id="visit">
  <h2 class="map-contact-title">
     <span class="map-contact-highlight">Venez nous rendre visite</span>
  </h2>

  <div class="map-contact-grid">
    <div class="map-contact-left">
      <div class="map-contact-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3197.2178951529513!2d3.2835529!3d36.7413406!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x128e45000a39c62f%3A0x2e7348a7577dd8bc!2sMB%20carsdz!5e0!3m2!1sfr!2sdz!4v1763054990439!5m2!1sfr!2sdz"
          loading="lazy"
          allowfullscreen
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
    </div>

    <div class="map-contact-separator" aria-hidden="true"></div>

    <div class="map-contact-right">
      <p class="map-contact-label">Agence MB CARS DZ</p>
      <p class="map-contact-address">
        Rouiba, Alger<br>
        À proximité de l’autoroute, facile d’accès.
      </p>
      <p class="map-contact-hours">
        Ouvert 7j/7 — 24h/24.<br>
        Livraison possible à l’aéroport d’Alger et sur demande.
      </p>

      <a
        href="https://www.google.com/maps/dir/?api=1&destination=36.7413406,3.2835529"
        target="_blank"
        class="map-contact-button"
      >
        📍 Itinéraire vers l’agence
      </a>

      <p class="map-contact-note">
        Un clic, et votre GPS vous guide directement jusqu’à MB CARS DZ ROUIBA.
      </p>
    </div>
  </div>
</section>


<a href="https://wa.me/213656697788" class="whatsapp-float" target="_blank" aria-label="WhatsApp">
  <svg viewBox="0 0 32 32" class="wa-icon"><path fill="currentColor" d="M16 .5C7.3.5.2 7.6.2 16.3c0 2.9.8 5.7 2.3 8.2L.5 31.5l7.2-2c2.4 1.3 5.1 2 8 2h.1C24.7 31.5 31.8 24.4 31.8 15.7 31.8 7 24.7 0 16 .5zm9.4 22.5c-.4 1.1-2.1 2-3 2.1-.8.1-1.8.1-2.9-.2-1.7-.5-3.9-1.2-6.2-3.4-2.3-2.1-3.8-4.7-4.3-5.5-.4-.8-1-2.4-.1-3.5.5-.7 1.5-2.4 2.5-2.4.6 0 .9 0 1.3.1.3.1.6.1.8.6.3.7 1.1 2.4 1.2 2.6.1.2.1.5 0 .7-.2.4-.3.6-.6.9-.3.3-.6.6-.2 1.2.4.6 1.8 3 4.3 4.9 3 2.2 3.6 1.8 4.3 1.7.7-.1 1.7-.7 2.1-1.3.3-.6.9-.7 1.5-.5.6.2 3.8 1.9 3.8 1.9.5.2.8.4.9.7.2.4 0 1.5-.3 2.1z"/></svg>
</a>

<!-- Script pour le sous-menu du footer -->
<script>
document.addEventListener('click', function (e) {
  const trigger = e.target.closest('.footer .nav-has-submenu > .nav-parent');
  if (!trigger) return;

  e.preventDefault();

  const li = trigger.closest('.nav-has-submenu');
  if (!li) return;

  li.classList.toggle('open');
});
</script>

<?php
// Footer global
include __DIR__ . '/partials/footer.php';
?>

</body>
</html>

