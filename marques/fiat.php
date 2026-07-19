<?php
// ===============================
//  PAGE MARQUE : FIAT
// ===============================
$brand     = 'Fiat';
$pageTitle       = 'Location Fiat à Alger | MB CARS DZ';
$pageDescription = 'Fiat 500, Tipo, Doblo à Alger — location voiture Fiat avec assurance incluse. MB CARS DZ Rouiba.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/marques/fiat.php';
$base      = '../'; // remonte à la racine (index, assets, styles, etc.)

// Charge le tableau $vehicles
require __DIR__ . '/../data/vehicles.php';

// Filtre uniquement les véhicules Peugeot
$brandVehicles = array_filter($vehicles, function ($v) use ($brand) {
    return isset($v['brand']) && $v['brand'] === $brand;
});

// Header global
include __DIR__ . '/../partials/header.php';
?>

<main class="wrap brand-page">

  

  <!-- Titre de la page marque -->
  <section class="brand-header">
    <p class="brand-kicker">Marque</p>
    <h1 class="brand-title"><?php echo htmlspecialchars($brand); ?></h1>
    <div class="brand-bar">
      <span></span>
    </div>
  </section>

  <!-- Cartes véhicules de la marque -->
  <section class="vehicles marque-vehicles">
    <div class="veh-row no-slider">
      <div class="brand-cards">

        <?php if (!empty($brandVehicles)): ?>
          <?php foreach ($brandVehicles as $veh): ?>
            <?php
              // Sécurisation des champs
              $fullName = isset($veh['full_name']) ? $veh['full_name'] : ($veh['brand'] . ' ' . $veh['model']);
              $price    = isset($veh['price']) ? $veh['price'] : '';
              $image    = isset($veh['image']) ? $veh['image'] : '';
              $logo     = isset($veh['logo'])  ? $veh['logo']  : '';
            ?>

            <article class="card-veh" data-title="<?php echo htmlspecialchars($fullName); ?>">

              <!-- En-tête carte (logo + modèle) -->
              <div class="head">
                <?php if (!empty($logo)): ?>
                  <img
                    class="brand-logo"
                    src="<?php echo $base . htmlspecialchars($logo); ?>"
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
              <?php if (!empty($image)): ?>
                <img
                  class="photo"
                  loading="lazy"
                  src="<?php echo $base . htmlspecialchars($image); ?>"
                  alt="<?php echo htmlspecialchars($fullName); ?>"
                >
              <?php endif; ?>

              <!-- Note "photos non contractuelles" (AFFICHÉE pour Peugeot) -->
              <p class="card-note">
                *Photos non contractuelles&nbsp;: modèle, année ou couleur peuvent varier selon disponibilité.
              </p>

              <!-- Bouton conditions -->
              <a href="<?php echo $base; ?>conditions.php" class="btn-conditions">
                Voir nos conditions
              </a>

              <!-- Corps carte (prix + badges) -->
              <div class="body">
                <div class="price">
                  <?php echo htmlspecialchars($price); ?>
                  <small>Prix / jour</small>
                </div>
                <div class="badges">
                  <span class="pill">✅ Assurance incluse</span>
                  <span class="pill">✈️ Livraison offerte à l’aéroport d’Alger</span>
                  <span class="pill">⤵️ Prix dégressif selon la durée</span>
                </div>
              </div>

              <!-- Actions carte -->
              <div class="actions">
                <a class="btn btn-call" href="tel:+213656697788">
                  Nous appeler
                </a>
                <button
                  class="btn btn-gold"
                  type="button"
                  data-ask-car="<?php echo htmlspecialchars($fullName); ?>"
                >
                  Faire une demande
                </button>
              </div>

            </article>
          <?php endforeach; ?>

        <?php else: ?>
          <p style="color:#fff; text-align:center; margin:20px 0;">
            Aucun véhicule <?php echo htmlspecialchars($brand); ?> n’est disponible pour l’instant.
          </p>
        <?php endif; ?>

      </div>
    </div>
    <!-- Bouton retour -->
  <div class="back-bar">
    <a href="<?php echo $base; ?>index.html" class="btn-gold-rect btn-back-home">
      ← Retour à l’accueil
    </a>
  </div>
    <script src="<?php echo $base; ?>app.js" defer></script>
  </section>
</main>

<?php
// Footer global
include __DIR__ . '/../partials/footer.php';
?>
