<?php
// ===============================
//  CONDITIONS DE LOCATION
// ===============================
$pageTitle = 'Conditions de location — MB CARS DZ';
$base      = './'; // on est à la racine

include __DIR__ . '/partials/header.php';
?>

<main class="page-conditions wrap">

  <!-- HEADER DE PAGE -->
  <header class="page-conditions-header">
    <p class="pc-kicker">MBCARSDZ</p>
    <h1 class="pc-title">Conditions de location</h1>
    <p class="pc-sub">
      Afin de garantir une expérience de location transparente, sécurisée et professionnelle,
      MBCARSDZ applique les conditions suivantes pour chaque location.
    </p>
  </header>

  <!-- LISTE DES CONDITIONS -->
  <section class="pc-grid">

    <!-- 1. Conducteurs autorisés -->
    <article class="pc-card">
      <div class="pc-icon">👤</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          1. Conducteurs autorisés
        </h2>
        <p>
          Seuls les conducteurs mentionnés sur le contrat de location sont légalement autorisés à conduire le véhicule.
        </p>
        <p>
          Le client s’engage à ne confier le volant à aucune autre personne non déclarée.
          Toute infraction à cette règle engage la responsabilité totale du locataire.
        </p>
      </div>
    </article>

    <!-- 2. Âge minimum -->
    <article class="pc-card">
      <div class="pc-icon">🎫</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          2. Âge minimum du conducteur
        </h2>
        <p>
          Le conducteur principal et tout conducteur additionnel doivent être âgés d’au moins
          <strong>25 ans</strong> et disposer d’un permis de conduire en cours de validité de plus de 2 ans.
        </p>
      </div>
    </article>

    <!-- 3. Vérification du véhicule -->
    <article class="pc-card">
      <div class="pc-icon">🔍</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          3. Vérification du véhicule à la remise
        </h2>
        <p>
          Lors de la prise en charge, le client est invité à vérifier soigneusement&nbsp;:
        </p>
        <ul>
          <li>l’état extérieur du véhicule,</li>
          <li>l’état intérieur,</li>
          <li>les équipements et accessoires fournis.</li>
        </ul>
        <p>
          Toute anomalie doit être signalée immédiatement avant le départ.
        </p>
      </div>
    </article>

    <!-- 4. Responsabilité des dommages -->
    <article class="pc-card">
      <div class="pc-icon">⚠️</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          4. Responsabilité des dommages
        </h2>
        <p>
          Le véhicule doit être restitué dans le même état qu’au moment de la remise.
        </p>
        <ul>
          <li>Les consommables (huile, eau, carburant, etc.) sont à la charge de la société.</li>
          <li>
            Toute dégradation extérieure ou intérieure (rayures, bosses, bris, mauvaise utilisation…)
            sera facturée au client.
          </li>
        </ul>
      </div>
    </article>

    <!-- 5. Sortie du territoire -->
    <article class="pc-card">
      <div class="pc-icon">🌍</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          5. Sortie du territoire national
        </h2>
        <p>
          Il est formellement interdit de sortir le véhicule du territoire algérien sans autorisation
          écrite et préalable du propriétaire.
        </p>
        <p>
          Toute sortie non déclarée sera considérée comme une infraction grave assimilée à un vol,
          pouvant entraîner des poursuites judiciaires.
        </p>
      </div>
    </article>

    <!-- 6. Restitution & propreté -->
    <article class="pc-card">
      <div class="pc-icon">🧽</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          6. Restitution et propreté du véhicule
        </h2>
        <p>
          Le véhicule doit être rendu dans un état de propreté similaire à celui de la remise.
        </p>
        <p>
          En cas de restitution sale, des frais de nettoyage de
          <strong>1000&nbsp;DA (ou 5&nbsp;€)</strong> seront automatiquement appliqués.
        </p>
      </div>
    </article>

    <!-- 7. Caution -->
    <article class="pc-card">
      <div class="pc-icon">💳</div>
      <div class="pc-content">
        <h2 class="pc-card-title">
          7. Caution et kilométrage
        </h2>
        <p>
          Une caution est exigée pour chaque location. Elle varie selon la catégorie du véhicule&nbsp;:
        </p>
        <ul>
          <li>de <strong>250&nbsp;€ à 2000&nbsp;€</strong>,</li>
          <li>soit de <strong>50&nbsp;000&nbsp;DA à 500&nbsp;000&nbsp;DA</strong>.</li>
        </ul>
        <p>
          La caution est totalement restituée en l’absence de dommages ou de non-respect des conditions.
        </p>
        
         <p>
          Le kilométrage n'est pas ilimité.</p>
          <p>Le forfait  kilométrique varie entre
          250 km et 300 km par jour selon le véhicule loué.</p>
          <p>En cas de dépassement du kilométrage journalier autorisé,</p>
          <p>une facturation supplémentaire sera appliquée conformément au tarif
          en vigueur.
        </p>
      </div>
    </article>

  </section>

</main>

<?php
include __DIR__ . '/partials/footer.php';
?>
