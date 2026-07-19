<?php
/*
 * Partial réutilisable pour les pages villes / location voiture [ville].
 *
 * Variables attendues avant l'include :
 *   $cityName        — ex: "Alger"
 *   $cityNameFull    — ex: "Alger (Wilaya 16)"   (optionnel, sinon = $cityName)
 *   $citySlug        — ex: "alger"
 *   $cityParticles   — "à" | "à" | "en" etc.
 *   $cityIntro       — texte HTML de l'intro (2-3 §)
 *   $cityFAQs        — tableau [['q'=>'…','a'=>'…'], …]
 *   $cityBreadcrumb  — label du fil d'ariane
 *   $whatsappText    — message WhatsApp pré-rempli (urlencodé dans la page)
 *   $vehicles        — tableau complet des véhicules (chargé avant include)
 */

$cityNameFull  = $cityNameFull  ?? $cityName;
$cityParticle  = $cityParticle  ?? 'à';
$whatsappText  = rawurlencode("Bonjour, je souhaite louer une voiture {$cityParticle} {$cityName}.\nNom : \nDates : du … au …\nTéléphone : ");

// On sélectionne 3 véhicules représentatifs
$showcase = [];
$cats = ['suv','berline','citadine'];
foreach ($cats as $cat) {
    foreach ($vehicles as $v) {
        if (($v['category'] ?? '') === $cat) {
            $showcase[] = $v;
            break;
        }
    }
}
?>

<main class="page-city">

  <!-- Fil d'Ariane -->
  <nav class="city-breadcrumb wrap" aria-label="Fil d'Ariane">
    <ol style="display:flex;gap:6px;list-style:none;padding:16px 0;margin:0;font-size:.85rem;color:#aaa;flex-wrap:wrap">
      <li><a href="<?php echo $base; ?>index.php" style="color:#ffc43a">Accueil</a></li>
      <li style="margin:0 4px">/</li>
      <li aria-current="page" style="color:#fff"><?php echo htmlspecialchars($cityBreadcrumb ?? "Location voiture {$cityName}", ENT_QUOTES, 'UTF-8'); ?></li>
    </ol>
  </nav>

  <!-- Hero de page -->
  <header class="city-hero" style="background:linear-gradient(135deg,#0f0f0f 0%,#1a1a1a 100%);padding:56px 20px 40px;text-align:center;border-bottom:2px solid #d4af37">
    <div class="wrap" style="max-width:820px;margin:0 auto">
      <p style="color:#ffc43a;font-size:.85rem;text-transform:uppercase;letter-spacing:.12em;font-weight:600;margin-bottom:12px">MB CARS DZ</p>
      <h1 style="font-size:clamp(1.6rem,4vw,2.4rem);font-weight:800;line-height:1.2;margin-bottom:16px;color:#fff">
        Location de voiture <?php echo htmlspecialchars($cityParticle . ' ' . $cityName, ENT_QUOTES, 'UTF-8'); ?>
      </h1>
      <p style="color:#ccc;font-size:1.05rem;line-height:1.65;max-width:620px;margin:0 auto 28px">
        <?php echo $cityTagline ?? "Louez un véhicule récent {$cityParticle} {$cityName} avec assurance incluse. Livraison sur place. Prix transparents."; ?>
      </p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="#demandeForm" class="btn btn-gold" style="padding:12px 28px;font-weight:700;border-radius:999px;background:#d4af37;color:#000;text-decoration:none">Faire une demande</a>
        <a href="https://wa.me/213656697788?text=<?php echo $whatsappText; ?>" target="_blank" rel="noopener" class="btn" style="padding:12px 28px;font-weight:700;border-radius:999px;background:#25D366;color:#fff;text-decoration:none">WhatsApp</a>
      </div>
    </div>
  </header>

  <!-- INTRO -->
  <section class="city-intro wrap" style="max-width:860px;margin:0 auto;padding:44px 20px 32px">
    <?php echo $cityIntro; ?>
  </section>

  <!-- AVANTAGES -->
  <section class="city-avantages wrap" style="max-width:900px;margin:0 auto;padding:0 20px 44px">
    <h2 style="font-size:1.3rem;font-weight:700;color:#ffc43a;margin-bottom:24px;text-align:center">Pourquoi choisir MB CARS DZ ?</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">
      <?php
      $avantages = [
        ['icon'=>'🛡️','title'=>'Assurance incluse','text'=>'Chaque véhicule est assuré. Prix affiché = prix final.'],
        ['icon'=>'🚗','title'=>'Flotte récente','text'=>'SUV, berlines et citadines des dernières années, bien entretenus.'],
        ['icon'=>'💬','title'=>'Contact direct','text'=>'Vous parlez directement avec le propriétaire. Service humain, réactif.'],
        ['icon'=>'📍','title'=>'Livraison','text'=>"Livraison {$cityParticle} {$cityName} et à l'aéroport d'Alger sur demande."],
        ['icon'=>'💰','title'=>'Prix dégressifs','text'=>'Plus vous louez longtemps, moins c\'est cher. Tarifs adaptés.'],
        ['icon'=>'⏱️','title'=>'Réponse 24h','text'=>'Confirmation de réservation sous 24 heures maximum.'],
      ];
      foreach ($avantages as $av): ?>
      <div style="background:#111;border:1px solid #222;border-radius:14px;padding:20px 16px;text-align:center">
        <div style="font-size:2rem;margin-bottom:8px"><?php echo $av['icon']; ?></div>
        <h3 style="font-size:.95rem;font-weight:700;color:#fff;margin:0 0 6px"><?php echo $av['title']; ?></h3>
        <p style="font-size:.85rem;color:#aaa;margin:0;line-height:1.5"><?php echo $av['text']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- VÉHICULES -->
  <section class="city-vehicles wrap" style="max-width:900px;margin:0 auto;padding:0 20px 48px">
    <h2 style="font-size:1.3rem;font-weight:700;color:#ffc43a;margin-bottom:24px;text-align:center">
      Véhicules disponibles <?php echo htmlspecialchars($cityParticle . ' ' . $cityName, ENT_QUOTES, 'UTF-8'); ?>
    </h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px">
      <?php foreach ($showcase as $v):
        $fn  = htmlspecialchars($v['full_name'], ENT_QUOTES, 'UTF-8');
        $pr  = htmlspecialchars($v['price'],     ENT_QUOTES, 'UTF-8');
        $img = htmlspecialchars($v['image'],     ENT_QUOTES, 'UTF-8');
        $lg  = htmlspecialchars($v['logo'],      ENT_QUOTES, 'UTF-8');
        $br  = htmlspecialchars($v['brand'],     ENT_QUOTES, 'UTF-8');
      ?>
      <article style="background:#111;border:1px solid #222;border-radius:16px;overflow:hidden">
        <img src="<?php echo $base . $img; ?>" alt="<?php echo $fn; ?> — location <?php echo htmlspecialchars($cityParticle . ' ' . $cityName, ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;height:170px;object-fit:cover" loading="lazy">
        <div style="padding:14px">
          <p style="margin:0 0 4px;font-weight:700;color:#fff;font-size:.95rem"><?php echo $fn; ?></p>
          <p style="margin:0 0 12px;font-size:.85rem;color:#ffc43a;font-weight:600"><?php echo $pr; ?></p>
          <button class="btn-ask btn" data-ask-car="<?php echo $fn; ?>" style="width:100%;background:#d4af37;color:#000;font-weight:700;border:none;border-radius:999px;padding:8px 0;cursor:pointer;font-size:.9rem">Demander ce véhicule</button>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:20px">
      <a href="<?php echo $base; ?>index.php#vehicles" style="color:#ffc43a;font-weight:600">Voir toute la flotte →</a>
    </p>
  </section>

  <!-- COMMENT ÇA MARCHE -->
  <section class="city-steps wrap" style="max-width:860px;margin:0 auto;padding:0 20px 48px">
    <h2 style="font-size:1.3rem;font-weight:700;color:#ffc43a;margin-bottom:24px;text-align:center">Comment louer une voiture <?php echo htmlspecialchars($cityParticle . ' ' . $cityName, ENT_QUOTES, 'UTF-8'); ?> ?</h2>
    <ol style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;list-style:none;padding:0;margin:0">
      <li style="background:#111;border:1px solid #222;border-radius:14px;padding:24px 16px;text-align:center">
        <div style="width:40px;height:40px;background:#d4af37;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:#000;margin:0 auto 12px">1</div>
        <h3 style="font-size:.95rem;font-weight:700;color:#fff;margin:0 0 8px">Choisissez un véhicule</h3>
        <p style="font-size:.85rem;color:#aaa;margin:0">Parcourez notre flotte et sélectionnez le modèle qui vous convient.</p>
      </li>
      <li style="background:#111;border:1px solid #222;border-radius:14px;padding:24px 16px;text-align:center">
        <div style="width:40px;height:40px;background:#d4af37;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:#000;margin:0 auto 12px">2</div>
        <h3 style="font-size:.95rem;font-weight:700;color:#fff;margin:0 0 8px">Envoyez votre demande</h3>
        <p style="font-size:.85rem;color:#aaa;margin:0">Via le formulaire, WhatsApp ou en appelant directement.</p>
      </li>
      <li style="background:#111;border:1px solid #222;border-radius:14px;padding:24px 16px;text-align:center">
        <div style="width:40px;height:40px;background:#d4af37;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:#000;margin:0 auto 12px">3</div>
        <h3 style="font-size:.95rem;font-weight:700;color:#fff;margin:0 0 8px">Confirmation sous 24h</h3>
        <p style="font-size:.85rem;color:#aaa;margin:0">On vous confirme et on organise la livraison selon vos besoins.</p>
      </li>
    </ol>
  </section>

  <!-- FAQ -->
  <section class="city-faq wrap" style="max-width:860px;margin:0 auto;padding:0 20px 48px">
    <h2 style="font-size:1.3rem;font-weight:700;color:#ffc43a;margin-bottom:24px;text-align:center">
      Questions fréquentes — Location voiture <?php echo htmlspecialchars($cityParticle . ' ' . $cityName, ENT_QUOTES, 'UTF-8'); ?>
    </h2>
    <div style="border-top:1px solid #2a2a2a">
      <?php foreach ($cityFAQs as $faq): ?>
      <details style="border-bottom:1px solid #2a2a2a">
        <summary style="padding:16px 4px;cursor:pointer;font-weight:600;color:#fff;font-size:1rem;list-style:none;display:flex;justify-content:space-between;align-items:center;gap:12px">
          <h3 style="margin:0;font-size:inherit;font-weight:inherit;color:inherit"><?php echo htmlspecialchars($faq['q'], ENT_QUOTES, 'UTF-8'); ?></h3>
          <span style="color:#ffc43a;font-size:1.4rem;flex-shrink:0">+</span>
        </summary>
        <div style="padding:0 4px 16px;color:#ccc;line-height:1.7;font-size:.95rem">
          <p style="margin:0"><?php echo $faq['a']; ?></p>
        </div>
      </details>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- FORMULAIRE RAPIDE -->
  <section class="city-cta wrap" id="demandeForm" style="max-width:700px;margin:0 auto;padding:0 20px 64px;text-align:center">
    <h2 style="font-size:1.3rem;font-weight:700;color:#ffc43a;margin-bottom:8px">Réserver votre voiture <?php echo htmlspecialchars($cityParticle . ' ' . $cityName, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p style="color:#aaa;margin-bottom:24px">Contactez-nous via WhatsApp ou par téléphone — nous répondons rapidement.</p>
    <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center">
      <a href="https://wa.me/213656697788?text=<?php echo $whatsappText; ?>" target="_blank" rel="noopener"
         style="padding:14px 32px;background:#25D366;color:#fff;border-radius:999px;font-weight:700;text-decoration:none;font-size:1rem">
        💬 WhatsApp
      </a>
      <a href="tel:+213656697788"
         style="padding:14px 32px;background:#d4af37;color:#000;border-radius:999px;font-weight:700;text-decoration:none;font-size:1rem">
        📞 +213 656 69 77 88
      </a>
    </div>
    <p style="margin-top:24px;font-size:.85rem;color:#777">Ou utilisez le <a href="<?php echo $base; ?>index.php#airportForm" style="color:#ffc43a">formulaire de demande complet →</a></p>
  </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
