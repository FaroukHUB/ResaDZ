<?php
// marque.php

require __DIR__ . '/data/vehicles.php';

// Priorité : variable locale $brand (fichier audi.php, bmw.php, etc.)
if (!isset($brand)) {
    $brand = $_GET['brand'] ?? '';
}

$brand = trim($brand);
if ($brand === '') {
    $brand = 'Audi'; // fallback
}

$filtered = array_values(array_filter($vehicles, function($v) use ($brand) {
    return mb_strtolower($v['brand']) === mb_strtolower($brand);
}));
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Location <?= htmlspecialchars($brand) ?> – MB Cars DZ</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <main class="wrap brand-page" style="padding-top:32px;padding-bottom:40px;">

    <div class="back-home">
      <a href="https://mbcarsdz.mon-agenceweb.fr/" class="btn-back">← Retour à l'accueil</a>
    </div>

    <header style="margin-bottom:24px;">
      <p class="sec-kicker">Location de véhicules</p>
      <h1 class="sec-title auto-effect">
        Nos véhicules <?= htmlspecialchars($brand) ?>
      </h1>
    </header>

    <?php if (empty($filtered)): ?>
      <p>Aucun véhicule <?= htmlspecialchars($brand) ?> n’est disponible pour le moment.</p>
    <?php else: ?>

      <section class="veh-row no-slider" style="gap:18px;flex-wrap:wrap;">
        <div class="cards-track brand-cards">
          <?php foreach ($filtered as $car): ?>
            <article class="card-veh" data-title="<?= htmlspecialchars($car['full_name']) ?>">

              <div class="head">
                <img class="brand-logo"
                     src="<?= htmlspecialchars($car['logo']) ?>"
                     alt="<?= htmlspecialchars($car['brand']) ?> logo"
                     loading="lazy">
                <h4 class="model">
                  <span><?= htmlspecialchars($car['brand']) ?></span>
                  <?= htmlspecialchars($car['model']) ?>
                </h4>
              </div>

              <!-- PHOTO -->
              <img class="photo"
                   src="<?= htmlspecialchars($car['image']) ?>"
                   alt="<?= htmlspecialchars($car['full_name']) ?>"
                   loading="lazy">

              <?php
              // ✅ Si le modèle contient deux années (ex: "2021 à 2023") → mention
              $isNonContractuelle =
                  isset($car['model'])
                  && preg_match('/\d{4}.*à.*\d{4}/u', $car['model']);

              if ($isNonContractuelle): ?>
                <p class="card-note">
                  Photo non contractuelle&nbsp;: plusieurs modèles existent selon les années et finitions.
                </p>
              <?php endif; ?>

              <div class="body">
                <div class="price">
                  <?= htmlspecialchars($car['price']) ?>
                  <small>Prix / jour</small>
                </div>
                <div class="badges">
                  <span class="pill">✅ Assurance incluse</span>
                  <span class="pill">🚗 Livraison offerte</span>
                  <span class="pill">⤵️ Prix dégressif selon la durée</span>
                </div>
              </div>

              <div class="actions">
                <a class="btn btn-call" href="tel:+213656697788" data-i18n="call">Nous appeler</a>
                <button class="btn btn-gold"
                        data-ask-car="<?= htmlspecialchars($car['full_name']) ?>">
                  Faire une demande
                </button>
              </div>

            </article>
          <?php endforeach; ?>
        </div>
      </section>

    <?php endif; ?>

  </main>

  <?php include 'footer.php'; ?>

  <script src="<?php echo $base; ?>app.js" defer></script>
</body>
</html>
