<?php
// ===============================
// PAGE 404 — MB CARS DZ
// ===============================

$pageTitle = "Erreur 404 — Page introuvable";
$pageDescription = "La page que vous cherchez n’existe pas ou a été déplacée.";
$base = './'; // racine du site

include __DIR__ . '/partials/header.php';
?>

<main class="wrap" style="padding-top:60px; padding-bottom:60px; text-align:center; color:#fff;">

    <h1 class="sec-title auto-effect" style="margin-bottom:20px;">
        Erreur 404
    </h1>

    <p style="font-size:1.2rem; max-width:600px; margin:0 auto 30px;">
        La page que vous cherchez n’existe pas, n’est plus disponible ou a été déplacée.
    </p>

    <img src="<?php echo $base; ?>assets/404-car.png"
         alt="Page non trouvée"
         style="max-width:300px; width:100%; margin:20px auto; display:block;">

    <a href="<?php echo $base; ?>index.php" class="btn-gold-rect" style="margin-top:25px; display:inline-block;">
        Retour à l’accueil
    </a>

</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
