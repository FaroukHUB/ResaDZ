<?php
// Mets ici EXACTEMENT la même chose que sur ta page SUV
// Exemple si ta page SUV commence comme ça :
// $base = './'; 
// $pageTitle = 'SUV — MB CARS DZ';
// include 'header.php';

// Alors pour la page contact :
$base = './';
$pageTitle = 'Nous contacter — MB CARS DZ';
// Header global
include __DIR__ . '/partials/header.php';

// --------- LOGIQUE FORMULAIRE ---------
$success = false;
$errors = [];

$nom = '';
$prenom = '';
$email = '';
$telephone = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if ($nom === '')       $errors[] = "Le nom est obligatoire.";
    if ($prenom === '')    $errors[] = "Le prénom est obligatoire.";
    if ($email === '')     $errors[] = "L'email est obligatoire.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    if ($telephone === '') $errors[] = "Le numéro de téléphone est obligatoire.";
    if ($message === '')   $errors[] = "Le message est obligatoire.";

    if (empty($errors)) {
        $to = "contact@mbcarsdzrouiba.com";
        $subject = "📩 Nouveau message depuis MB CARS DZ";

        $body  = "Nom : $nom\n";
        $body .= "Prénom : $prenom\n";
        $body .= "Email : $email\n";
        $body .= "Téléphone : $telephone\n\n";
        $body .= "Message :\n$message\n";

        $headers  = "From: no-reply@mbcarsdz.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $subject, $body, $headers)) {
            $success = true;
            $nom = $prenom = $email = $telephone = $message = '';
        } else {
            $errors[] = "Erreur lors de l'envoi du message. Réessayez plus tard.";
        }
    }
}
?>

<div class="wrap" style="padding-top:120px; padding-bottom:80px;">

  <h1 class="brand-title" style="color:#FFD700; margin-bottom:20px;">
    Nous contacter
  </h1>

  <p style="max-width:700px; color:#ccc; margin-bottom:40px; line-height:1.6;">
    Une question sur un véhicule ? Envie d’un conseil ou d’un accompagnement&nbsp;?
    Remplissez le formulaire ci-dessous, notre équipe MB Cars DZ vous répond rapidement.
  </p>

  <?php if ($success): ?>
    <div style="background:#0f3f1d; color:#c5f7d2; padding:15px; border-radius:8px; margin-bottom:30px;">
      Votre message a bien été envoyé.
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div style="background:#3f0f0f; color:#f7c5c5; padding:15px; border-radius:8px; margin-bottom:30px;">
      <strong>Merci de corriger les erreurs :</strong>
      <ul style="margin-top:8px;">
        <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

    <style>
    /* Styles spécifiques au formulaire de contact uniquement */
    .contact-form label {
      color: #f5f5f5;
      display: block;
      margin-bottom: 6px;
      font-size: 0.95rem;
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 8px 10px;
      border-radius: 4px;
      border: 1px solid #444;
      background: #111111;
      color: #f5f5f5;
    }

    .contact-form input::placeholder,
    .contact-form textarea::placeholder {
      color: #777777;
    }
  </style>

  <form method="post" action="contact.php"
        class="contact-form"
        style="background:#111; padding:25px; border-radius:10px; border:1px solid #333;">

    <div style="display:flex; gap:20px; flex-wrap:wrap;">
      <div style="flex:1; min-width:260px;">
        <label>Nom *</label>
        <input type="text" name="nom" required
               value="<?php echo htmlspecialchars($nom); ?>">
      </div>

      <div style="flex:1; min-width:260px;">
        <label>Prénom *</label>
        <input type="text" name="prenom" required
               value="<?php echo htmlspecialchars($prenom); ?>">
      </div>
    </div>

    <div style="display:flex; gap:20px; margin-top:20px; flex-wrap:wrap;">
      <div style="flex:1; min-width:260px;">
        <label>Email *</label>
        <input type="email" name="email" required
               value="<?php echo htmlspecialchars($email); ?>">
      </div>

      <div style="flex:1; min-width:260px;">
        <label>Téléphone *</label>
        <input type="text" name="telephone" required
               value="<?php echo htmlspecialchars($telephone); ?>">
      </div>
    </div>

    <div style="margin-top:20px;">
      <label>Message *</label>
      <textarea name="message" rows="6" required><?php
          echo htmlspecialchars($message);
      ?></textarea>
    </div>

    <button type="submit"
            style="margin-top:25px; padding:12px 35px; font-weight:600; border:none; border-radius:40px;
                   background:linear-gradient(135deg,#c9a15a,#f5d984); color:#000; cursor:pointer;">
      Envoyer
    </button>
  </form>

</div>


<style>
  /* ===== Bloc carte + itinéraire MB CARS DZ ===== */
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
    <!-- Colonne gauche : carte -->
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

    <!-- Séparateur doré -->
    <div class="map-contact-separator" aria-hidden="true"></div>

    <!-- Colonne droite : adresse + bouton -->
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



<?php
// Footer global
include __DIR__ . '/partials/footer.php';
?>
