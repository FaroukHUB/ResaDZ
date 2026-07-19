<?php
// suv.php — Page catégorie SUV

$base            = './';
$pageTitle       = 'Location SUV à Alger — BMW X3, VW Tiguan, Mercedes GLE | MB CARS DZ';
$pageDescription = 'Louez un SUV à Alger chez MB CARS DZ : BMW X3, VW Tiguan, Mercedes GLE, Hyundai Tucson et plus. Assurance incluse, livraison aéroport. À partir de 20 000 DA/jour.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/suv.php';

// Tableau global des véhicules
require __DIR__ . '/data/vehicles.php';

// On ne garde que les SUV
$suvVehicles = array_filter($vehicles, function ($v) {
    return isset($v['category']) && $v['category'] === 'suv';
});

// Header global
include __DIR__ . '/partials/header.php';
?>

<main class="wrap category-page">

  <!-- Titre de la page catégorie -->
  <section class="brand-header">
    <p class="brand-kicker">Catégorie</p>
    <h1 class="brand-title">SUV</h1>
    <div class="brand-bar">
      <span></span>
    </div>
  </section>

  <!-- Cartes SUV : SANS SLIDER, même design que la home -->
  <section class="vehicles marque-vehicles">
    <div class="veh-row no-slider">
      <div class="brand-cards">
        <?php if (!empty($suvVehicles)): ?>
          <?php foreach ($suvVehicles as $veh): ?>
            <article
              class="card-veh"
              data-title="<?php echo htmlspecialchars($veh['full_name']); ?>"
            >
              <!-- En-tête carte (logo + modèle) -->
              <div class="head">
                <?php if (!empty($veh['logo'])): ?>
                  <img
                    class="brand-logo"
                    src="<?php echo htmlspecialchars($veh['logo']); ?>"
                    alt="<?php echo htmlspecialchars($veh['brand']); ?>"
                    loading="lazy"
                  >
                <?php endif; ?>

                <h4 class="model">
                  <span><?php echo htmlspecialchars($veh['brand']); ?></span>
                  <?php echo htmlspecialchars($veh['model']); ?>
                </h4>
              </div>

              <!-- Photo véhicule -->
              <?php if (!empty($veh['image'])): ?>
                <img
                  class="photo"
                  src="<?php echo htmlspecialchars($veh['image']); ?>"
                  alt="<?php echo htmlspecialchars($veh['full_name']); ?>"
                  loading="lazy"
                >
              <?php endif; ?>

              <!-- Bande "Voir nos conditions" sous la photo -->
              <div class="card-note card-note-inline">
                <a href="/conditions.php" class="btn-conditions">
                  Voir nos conditions
                </a>
              </div>

              <!-- Corps carte (prix + badges) -->
              <div class="body">
                <div class="price">
                  <?php echo htmlspecialchars($veh['price']); ?>
                  <small>Prix / jour</small>
                </div>

                <div class="badges">
                  <span class="pill">✅ Assurance incluse</span>
                  <span class="pill">✈️ Livraison offerte à l’aéroport d’Alger</span>
                  <span class="pill">⤵️ Prix dégressif selon la durée</span>
                </div>
              </div>

              <!-- Boutons -->
              <div class="actions">
                <a
                  class="btn btn-call"
                  href="tel:+213656697788"
                  data-i18n="call"
                >
                  Nous appeler
                </a>
                <button
                  class="btn btn-gold"
                  type="button"
                  data-ask-car="<?php echo htmlspecialchars($veh['full_name']); ?>"
                >
                  Faire une demande
                </button>
              </div>
            </article>
          <?php endforeach; ?>

        <?php else: ?>
          <p style="color:#fff; text-align:center; margin:20px 0;">
            Aucun SUV n’est disponible pour l’instant.
          </p>
        <?php endif; ?>
      </div>
    </div>
  </section>

</main>

<?php
// Footer global
include __DIR__ . '/partials/footer.php';
?>
