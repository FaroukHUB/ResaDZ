<?php
// Sécurité sur les variables
if (!isset($base)) {
    $base = './';
}
if (!isset($pageTitle)) {
    $pageTitle = 'MB CARS DZ — Location de véhicules';
}
$canonicalUrl = $canonicalUrl ?? 'https://mbcarsdzrouiba.com/';
$ogImageUrl   = $ogImageUrl   ?? 'https://mbcarsdzrouiba.com/assets/hero.webp';
$ogType       = $ogType       ?? 'website';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" href="<?php echo $base; ?>assets/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo $base; ?>assets/favicon.ico" type="image/x-icon">
<link rel="icon" type="image/svg+xml" href="<?php echo $base; ?>assets/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base; ?>assets/favicon.png">
<link rel="apple-touch-icon" href="<?php echo $base; ?>assets/apple-touch-icon.png">

<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">

<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle ?? 'MB CARS DZ', ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="MB CARS DZ">
<meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription ?? '', ENT_QUOTES, 'UTF-8'); ?>">
<meta name="google-site-verification" content="-HCPPUz3AGhE0f9SgqUYv54CL9dcmSn4-g4bo_ruqUk" />
  <!-- Titre avec fallback -->
  <title><?php echo htmlspecialchars($pageTitle ?? 'MB CARS DZ', ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Meta description avec fallback obligatoire -->
  <?php
    $fallbackDescription = "MB CARS DZ — Location de voitures à Alger : SUV, berlines et citadines récentes avec assurance incluse et livraison aéroport d'Alger.";
    $desc = !empty($pageDescription) ? $pageDescription : $fallbackDescription;
  ?>
  <meta name="description"
        content="<?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?>">

  <!-- CSS principal -->
  <link rel="stylesheet" href="<?php echo $base; ?>styles.css">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap&subset=latin-ext" rel="stylesheet">

<?php if (!empty($pageSchema)): ?>
<script type="application/ld+json"><?php echo $pageSchema; ?></script>
<?php endif; ?>
</head>


<body class="antialiased">

<header class="site-header">

  <!-- TOP BAR -->
  <div class="topbar">
    <div class="wrap topbar-row">

      <!-- Logo -->
      <div class="header-logo-group">
        <a class="brand" href="<?php echo $base; ?>index.php">
          <img src="<?php echo $base; ?>assets/logo.jpeg" alt="MB CARS DZ" />
        </a>
        <span class="logo-text">MBCARS DZ</span>
      </div>

      <!-- Search (desktop uniquement) -->
      <div class="search desktop-only">
        <form action="<?php echo $base; ?>recherche.php" method="get" class="search-form">
          <input
            type="text"
            name="q"
            id="globalSearchInput"
            value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
            data-i18n-ph="searchPH"
            placeholder="Rechercher un véhicule (marque, modèle)…"
            aria-label="Recherche"
          />
          <button type="submit" class="search-submit" aria-label="Lancer la recherche">
            <svg class="i i-search" viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M21 21l-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
          </button>
        </form>
      </div>

      <!-- Actions (langues + hamburger mobile) -->
      <div class="actions">

        <!-- Select langue (desktop) -->
        <label class="lang desktop-only">
          <select id="langSelect" aria-label="Langue">
            <option value="fr" selected>🇫🇷 Français</option>
            <option value="en">🇬🇧 English</option>
            <option value="ar">🇩🇿 العربية</option>
          </select>
        </label>

        <!-- Lang switcher mobile -->
        <div class="lang-mobile mobile-only" tabindex="-1">
          <button
            class="flag-btn"
            type="button"
            aria-haspopup="true"
            aria-controls="langDropMobile"
          >
            🌍
          </button>
          <ul id="langDropMobile" class="lang-drop" role="menu" aria-label="Choisir la langue">
            <li data-lang="fr" role="menuitem">🇫🇷 Français</li>
            <li data-lang="ar" role="menuitem">🇩🇿 العربية</li>
            <li data-lang="en" role="menuitem">🇬🇧 English</li>
          </ul>
        </div>

        <!-- Hamburger (mobile) -->
        <button class="hamburger mobile-only" id="hamburger" aria-label="Menu">
          <span class="l1"></span><span class="l2"></span><span class="l3"></span>
        </button>

      </div>
    </div>
  </div> <!-- FIN TOPBAR -->


  <!-- NAVIGATION SOUS LA TOPBAR (DESKTOP) -->
  <nav class="navbar desktop-only">
    <div class="wrap">
      <ul class="nav-row">

        <li><a href="<?php echo $base; ?>index.php">Accueil</a></li>

        <li class="has-sub">
          <a class="nav-parent" href="#vehicules">
            Nos véhicules
            <span class="nav-arrow-down"></span>
          </a>
          <!-- Sous-menu masqué -->
          <ul class="nav-sub">
            <li><a href="<?php echo $base; ?>suv.php">SUV</a></li>
            <li><a href="<?php echo $base; ?>berlines.php">Berlines</a></li>
            <li><a href="<?php echo $base; ?>citadines.php">Citadines</a></li>
          </ul>
        </li>

        <li><a href="<?php echo $base; ?>conditions.php">Conditions de location</a></li>
        <li><a href="<?php echo $base; ?>nous-contacter.php">Nous contacter</a></li>
        <li><a href="<?php echo $base; ?>Apropos.php">À propos</a></li>

        <li class="has-sub">
          <a href="#" class="nav-parent">
            Rechercher par marque
            <span class="nav-arrow-down"></span>
          </a>
          <ul class="nav-sub nav-sub-brands">
            <li><a href="<?php echo $base; ?>marques/audi.php">Audi</a></li>
            <li><a href="<?php echo $base; ?>marques/bmw.php">BMW</a></li>
            <li><a href="<?php echo $base; ?>marques/dacia.php">Dacia</a></li>
            <li><a href="<?php echo $base; ?>marques/fiat.php">Fiat</a></li>
            <li><a href="<?php echo $base; ?>marques/hyundai.php">Hyundai</a></li>
            <li><a href="<?php echo $base; ?>marques/mercedes.php">Mercedes</a></li>
            <li><a href="<?php echo $base; ?>marques/peugeot.php">Peugeot</a></li>
            <li><a href="<?php echo $base; ?>marques/porsche.php">Porsche</a></li>
            <li><a href="<?php echo $base; ?>marques/renault.php">Renault</a></li>
            <li><a href="<?php echo $base; ?>marques/seat.php">Seat</a></li>
            <li><a href="<?php echo $base; ?>marques/skoda.php">Škoda</a></li>
            <li><a href="<?php echo $base; ?>marques/suzuki.php">Suzuki</a></li>
            <li><a href="<?php echo $base; ?>marques/volkswagen.php">Volkswagen</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </nav>

  <!-- PANNEAU MOBILE -->
  <div class="mobile-panel" id="mobilePanel">
    <div class="mp-inner">

      <div class="mp-block">
        <div class="search search-mobile">
          <input
            type="text"
            data-i18n-ph="searchPH"
            placeholder="Rechercher un véhicule…"
            aria-label="Recherche mobile"
          />
          <svg class="i i-search" viewBox="0 0 24 24" aria-hidden="true">
            <path
              d="M21 21l-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
            />
          </svg>
        </div>
      </div>

      <ul class="mp-nav">
        <li><a href="<?php echo $base; ?>index.php">Accueil</a></li>
        <li><a href="<?php echo $base; ?>Apropos.php">À propos</a></li>

        <li class="has-sub">
          <a href="#vehicules" class="nav-parent">
            Nos véhicules
            <span class="nav-arrow-down"></span>
          </a>
          <ul class="nav-sub">
            <li><a href="<?php echo $base; ?>suv.php">SUV</a></li>
            <li><a href="<?php echo $base; ?>berlines.php">Berlines</a></li>
            <li><a href="<?php echo $base; ?>citadines.php">Citadines</a></li>
          </ul>
        </li>

        <li><a href="<?php echo $base; ?>conditions.php">Conditions de location</a></li>
        <li><a href="<?php echo $base; ?>nous-contacter.php">Nous contacter</a></li>

        <li class="has-sub">
          <a href="#" class="nav-parent">
            Rechercher par marque
            <span class="nav-arrow-down"></span>
          </a>
          <ul class="nav-sub">
            <li><a href="<?php echo $base; ?>marques/audi.php">Audi</a></li>
            <li><a href="<?php echo $base; ?>marques/bmw.php">BMW</a></li>
            <li><a href="<?php echo $base; ?>marques/dacia.php">Dacia</a></li>
            <li><a href="<?php echo $base; ?>marques/fiat.php">Fiat</a></li>
            <li><a href="<?php echo $base; ?>marques/mercedes.php">Mercedes</a></li>
            <li><a href="<?php echo $base; ?>marques/renault.php">Renault</a></li>
            <!-- tu peux compléter ici si tu veux toutes les marques -->
          </ul>
        </li>
      </ul>

      <div class="mp-block contact-suite">
  <p class="mp-label">📞 Choisir un numéro</p>

  <select id="phoneSelect" class="phone-select">
    <option value="+213656697788">+213 656 69 77 88</option>
    <option value="+213560989543">+213 560 98 95 43</option>
    <option value="+213560989483">+213 560 98 94 83</option>
    <option value="+213560989541">+213 560 98 95 41</option>
  </select>

  <div class="contact-btns">
    <a id="btnCall" href="tel:+213656697788" class="btn-call-mini">Appeler</a>
    <a id="btnWa" href="https://wa.me/213656697788" class="btn-wa-mini">WhatsApp</a>
  </div>
</div>

      </div>

    </div>
  </div>

</header>
