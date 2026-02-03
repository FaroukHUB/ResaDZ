<?php
// ===============================
//  PAGE MARQUE : Mercedes
// ===============================
$brand     = 'Mercedes';
$pageTitle = 'Location Mercedes — MB CARS DZ';
$base      = '../'; // remonte à la racine

// Charge le tableau $vehicles
require __DIR__ . '/../data/vehicles.php';

// Filtre uniquement les véhicules Mercedes
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

  <!-- Cartes véhicules -->
  <section class="vehicles marque-vehicles">
    <div class="veh-row no-slider">
      <div class="brand-cards">

        <?php if (!empty($brandVehicles)): ?>
          <?php foreach ($brandVehicles as $veh): ?>
            <?php
              // Sécurisation
              $fullName = $veh['full_name'] ?? ($veh['brand'].' '.$veh['model']);
              $price    = $veh['price']    ?? '';
              $image    = $veh['image']    ?? '';
              $logo     = $veh['logo']     ?? '';

              // Détection "XXXX à XXXX"
              $isNonContractuelle = (
                isset($veh['model']) &&
                preg_match('/\d{4}\s*à\s*\d{4}/u', $veh['model'])
              );
            ?>

            <article class="card-veh" data-title="<?php echo htmlspecialchars($fullName); ?>">

              <!-- En-tête carte -->
              <div class="head">
                <?php if (!empty($logo)): ?>
                  <img
                    class="brand-logo"
                    src="<?php echo $base . htmlspecialchars($logo); ?>"
                    alt="<?php echo htmlspecialchars($veh['brand']); ?>"
                    loading="lazy">
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
                  alt="<?php echo htmlspecialchars($fullName); ?>">
              <?php endif; ?>

              <!-- Mention photo non contractuelle -->
              <?php if ($isNonContractuelle): ?>
                <p class="card-note" style="
                  color:#fff;
                  text-align:center;
                  font-size:0.85rem;
                  margin:10px 0 14px;
                  opacity:0.85;
                ">
                  *Photos non contractuelles
                </p>
              <?php endif; ?>

              <!-- Conditions -->
              <a href="<?php echo $base; ?>conditions.php" class="btn-conditions">
                Voir nos conditions
              </a>

              <!-- Corps carte -->
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

              <!-- Actions -->
              <div class="actions">
                <a class="btn btn-call" href="tel:+213656697788">
                  Nous appeler
                </a>
                <button
                  class="btn btn-gold"
                  type="button"
                  data-ask-car="<?php echo htmlspecialchars($fullName); ?>">
                  Faire une demande
                </button>
              </div>

            </article>
          <?php endforeach; ?>

        <?php else: ?>
          <p style="color:#fff;text-align:center;margin:20px 0;">
            Aucun véhicule <?php echo htmlspecialchars($brand); ?> n’est disponible pour l’instant.
          </p>
        <?php endif; ?>

      </div>
    </div>

    <!-- Retour -->
    <div class="back-bar">
      <a href="<?php echo $base; ?>index.html" class="btn-gold-rect btn-back-home">
        ← Retour à l’accueil
      </a>
    </div>

    <script src="<?php echo $base; ?>app.js" defer></script>

  </section>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
