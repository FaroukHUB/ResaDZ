<?php
// recherche.php — recherche globale sur toutes les voitures

$base      = './';
$pageTitle = 'Recherche de véhicules — MB CARS DZ';

// On charge tous les véhicules
require __DIR__ . '/data/vehicles.php';

// Récupère le terme de recherche
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

// Petit helper pour normaliser les strings
function mb_normalize_lower(string $str): string {
    $str = mb_strtolower($str, 'UTF-8');
    // on peut enlever les accents si besoin plus tard
    return $str;
}

if ($q !== '') {
    $needle = mb_normalize_lower($q);

    foreach ($vehicles as $v) {
        $brand     = $v['brand']     ?? '';
        $model     = $v['model']     ?? '';
        $full_name = $v['full_name'] ?? '';

        $haystack = mb_normalize_lower($brand . ' ' . $model . ' ' . $full_name);

        if (mb_strpos($haystack, $needle) !== false) {
            $results[] = $v;
        }
    }
}

include __DIR__ . '/partials/header.php';
?>

<main class="wrap brand-page search-page">

  <!-- Header de page -->
  <section class="brand-header">
    <p class="brand-kicker">Recherche</p>
    <h1 class="brand-title">
      <?php if ($q !== ''): ?>
        Résultats pour « <?php echo htmlspecialchars($q); ?> »
      <?php else: ?>
        Rechercher un véhicule
      <?php endif; ?>
    </h1>
    <div class="brand-bar"><span></span></div>
  </section>

  <!-- Résultats -->
  <section class="vehicles marque-vehicles">
    <div class="veh-row no-slider">
      <div class="brand-cards">

        <?php if ($q === ''): ?>

          <p style="color:#fff;text-align:center;margin:20px 0;">
            Saisissez une marque ou un modèle dans la barre de recherche en haut de la page.
          </p>

        <?php elseif (empty($results)): ?>

          <p style="color:#fff;text-align:center;margin:20px 0;">
            Aucun véhicule ne correspond à « <?php echo htmlspecialchars($q); ?> ».
          </p>

        <?php else: ?>

          <?php foreach ($results as $veh): ?>
            <article class="card-veh" data-title="<?php echo htmlspecialchars($veh['full_name']); ?>">

              <div class="head">
                <?php if (!empty($veh['logo'])): ?>
                  <img
                    class="brand-logo"
                    src="<?php echo $base . htmlspecialchars($veh['logo']); ?>"
                    alt="<?php echo htmlspecialchars($veh['brand']); ?>"
                    loading="lazy"
                  >
                <?php endif; ?>

                <h4 class="model">
                  <span><?php echo htmlspecialchars($veh['brand']); ?></span>
                  <?php echo htmlspecialchars($veh['model']); ?>
                </h4>
              </div>

              <?php if (!empty($veh['image'])): ?>
                <img
                  class="photo"
                  src="<?php echo $base . htmlspecialchars($veh['image']); ?>"
                  alt="<?php echo htmlspecialchars($veh['full_name']); ?>"
                  loading="lazy"
                >
              <?php endif; ?>

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

              <div class="actions">
                <a class="btn btn-call" href="tel:+213656697788">
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

        <?php endif; ?>

      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
