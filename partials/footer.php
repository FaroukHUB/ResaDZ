<?php
// footer.php
?>

<!-- Barre dorée de séparation -->
<div class="footer-gold-bar"></div>

<footer class="footer">
  <div class="wrap footer-top footer-centered">

    <!-- Colonne logo -->
    <div class="footer-col footer-brand">
      <img src="<?php echo $base; ?>assets/logo.jpeg" alt="MB Cars DZ" class="footer-logo" />
      <p class="footer-text">
        Agence de location de véhicules basée à Rouiba, Alger.<br>
        Service fiable, réactif et transparent.
      </p>
    </div>

    <!-- Coordonnées + numéros en badges -->
    <div class="footer-col footer-centered-block">
      <h4>Coordonnées</h4>
      <ul class="footer-list">
        <li>📍 Rouiba, Alger</li>
        <li>
          📧 <a href="mailto:bmcars.dz1982@gmail.com">contact@mbcarsdzrouiba.com</a>
        </li>
      </ul>

      <div class="footer-phones">
        <a href="tel:+213656697788" class="phone-badge">+213 656 69 77 88</a>
        <a href="tel:+213560989543" class="phone-badge">+213 560 98 95 43</a>
        <a href="tel:+213560989483" class="phone-badge">+213 560 98 94 83</a>
        <a href="tel:+213560989541" class="phone-badge">+213 560 98 95 41</a>
      </div>
    </div>

    <!-- Navigation -->
    <div class="footer-col footer-centered-block">
      <h4>Navigation</h4>
      <ul class="footer-list">
        <li><a href="<?php echo $base; ?>index.php">Accueil</a></li>
        <li><a href="<?php echo $base; ?>Apropos.php">À propos</a></li>

        <li class="nav-has-submenu">
          <a href="#" class="nav-parent">
            Nos véhicules
            <span class="nav-arrow-down"></span>
          </a>
          <ul class="nav-submenu">
            <li><a href="<?php echo $base; ?>suv.php">SUV</a></li>
            <li><a href="<?php echo $base; ?>berlines.php">Berlines</a></li>
            <li><a href="<?php echo $base; ?>citadines.php">Citadines</a></li>
          </ul>
        </li>

        <li><a href="<?php echo $base; ?>conditions.php">Conditions</a></li>
        <li><a href="<?php echo $base; ?>nous-contacter.php">Nous contacter</a></li>
      </ul>
    </div>

    <!-- Réseaux sociaux -->
    <div class="footer-col footer-centered-block">
      <h4>Réseaux</h4>
      <ul class="footer-social">
        <li>
          <a href="https://www.facebook.com/MBcarsdzrouiba" target="_blank" aria-label="Facebook">
            <img src="<?php echo $base; ?>icons/facebook.svg" alt="Facebook" class="social-icon">
          </a>
        </li>
        <li>
          <a href="https://www.instagram.com/mbcarsdzrouiba" target="_blank" aria-label="Instagram">
            <img src="<?php echo $base; ?>icons/instagram.svg" alt="Instagram" class="social-icon">
          </a>
        </li>
        <li>
          <a href="https://www.snapchat.com/add/MBcarsdzrouiba" target="_blank" aria-label="Snapchat">
            <img src="<?php echo $base; ?>icons/snapchat.svg" alt="Snapchat" class="social-icon">
          </a>
        </li>
        <li>
          <a href="https://www.tiktok.com/@mb.carsdzrouiba" target="_blank" aria-label="TikTok">
            <img src="<?php echo $base; ?>icons/tiktok.svg" alt="TikTok" class="social-icon">
          </a>
        </li>
        <li>
          <a href="https://twitter.com/MBcarsdzrouiba" target="_blank" aria-label="Twitter / X">
            <img src="<?php echo $base; ?>icons/twitter.svg" alt="Twitter / X" class="social-icon">
          </a>
        </li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom wrap">
    <p>© 2025 MB Cars DZ. Tous droits réservés.</p>
  </div>
</footer>

<!-- app.js -->
<script src="app.js" defer></script>



<!-- WhatsApp flottant -->
<a href="https://wa.me/213656697788" class="whatsapp-float" target="_blank" aria-label="WhatsApp">
  <svg viewBox="0 0 32 32" class="wa-icon">
    <path fill="currentColor" d="M16 .5C7.3.5.2 7.6.2 16.3c0 2.9.8 5.7 2.3 8.2L.5 31.5l7.2-2c2.4 1.3 5.1 2 8 2h.1C24.7 31.5 31.8 24.4 31.8 15.7 31.8 7 24.7 0 16 .5zm9.4 22.5c-.4 1.1-2.1 2-3 2.1-.8.1-1.8.1-2.9-.2-1.7-.5-3.9-1.2-6.2-3.4-2.3-2.1-3.8-4.7-4.3-5.5-.4-.8-1-2.4-.1-3.5.5-.7 1.5-2.4 2.5-2.4.6 0 .9 0 1.3.1.3.1.6.1.8.6.3.7 1.1 2.4 1.2 2.6.1.2.1.5 0 .7-.2.4-.3.6-.6.9-.3.3-.6.6-.2 1.2.4.6 1.8 3 4.3 4.9 3 2.2 3.6 1.8 4.3 1.7.7-.1 1.7-.7 2.1-1.3.3-.6.9-.7 1.5-.5.6.2 3.8 1.9 3.8 1.9.5.2.8.4.9.7.2.4 0 1.5-.3 2.1z"/>
  </svg>
</a>

<!-- MODALE DEMANDE DE LOCATION (unique pour tout le site) -->
<div id="askModal" class="ask-modal" aria-hidden="true">
  <div class="ask-backdrop" data-close></div>

  <div class="ask-dialog" role="dialog" aria-modal="true" aria-labelledby="askTitle">
    <!-- Bouton de fermeture -->
    <button class="ask-close" type="button" aria-label="Fermer" data-close>×</button>

    <h3 id="askTitle">Demande de location</h3>

    <form id="askForm">
      <!-- Véhicule ciblé (rempli par data-ask-car ou data-title) -->
      <input type="hidden" name="vehicle" id="askVehicle">

      <!-- Nom -->
      <div class="row">
        <label>
          Nom et prénom
          <input
            required
            name="name"
            type="text"
            placeholder="Votre nom"
          >
        </label>
      </div>

      <!-- Téléphone -->
      <div class="row">
        <label>
          Téléphone
          <input
            required
            name="phone"
            type="tel"
            placeholder="+213 …"
          >
        </label>
      </div>

      <!-- Dates début / fin -->
      <div class="row grid">
        <label for="dateStart">
          Début
          <input
            required
            name="start"
            id="dateStart"
            type="date"
          >
        </label>

        <label for="dateEnd">
          Fin
          <input
            required
            name="end"
            id="dateEnd"
            type="date"
          >
        </label>
      </div>

      <p class="hint" id="dateInfo">Sélectionnez vos dates.</p>

      <!-- Durée rapide -->
      <div class="row">
        <p class="lbl">Durée rapide</p>
        <div class="ask-quick">
          <button type="button" class="ask-quick-btn" data-duration="1">+1 jour</button>
          <button type="button" class="ask-quick-btn" data-duration="3">+3 jours</button>
          <button type="button" class="ask-quick-btn" data-duration="7">+7 jours</button>
          <button type="button" class="ask-quick-btn" data-duration="10">+10 jours</button>
        </div>
      </div>

      <!-- Message -->
      <div class="row">
        <label>
          Message
          <textarea
            name="message"
            rows="4"
            placeholder="Précisions utiles…"
          ></textarea>
        </label>
      </div>

      <!-- Actions -->
      <div class="actions">
        <button type="submit" class="btn btn-gold">
          Envoyer la demande
        </button>

        <a
          id="askWhatsApp"
          class="btn btn-call"
          target="_blank"
          rel="noopener"
          data-i18n="call"
        >
          Nous appeler
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Styles spécifiques footer + sous-menu -->
<style>
  /* BARRE DORÉE */
  .footer-gold-bar {
    width: 100%;
    height: 4px;
    background: linear-gradient(to right, #d4af37, #ffc43a, #d4af37);
    margin-bottom: 20px;
  }

  .footer-centered {
    text-align: center;
  }
  .footer-centered-block {
    text-align: center;
  }

  /* Liens du footer en jaune */
  .footer .footer-list a,
  .footer .footer-social a {
    color: #ffc43a;
  }
  .footer .footer-list a:hover,
  .footer .footer-social a:hover {
    filter: brightness(1.1);
  }

  /* Téléphones centrés et empilés */
  .footer .footer-phones {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }

  /* Sous-menu fermé par défaut */
  .footer .nav-submenu {
    display: none;
    margin-top: 4px;
  }
  .footer .nav-has-submenu.open .nav-submenu {
    display: block;
    padding-top: 8px;
  }
</style>

<!-- Script sous-menu footer + MODAL GLOBAL -->
<script>
(function () {
  // ===== SOUS-MENU "Nos véhicules" DANS LE FOOTER =====
  document.addEventListener("click", function (e) {
    const trigger = e.target.closest(".footer .nav-has-submenu > .nav-parent");
    if (!trigger) return;

    e.preventDefault();
    const li = trigger.closest(".nav-has-submenu");
    if (!li) return;
    li.classList.toggle("open");
  });

  // ===== MODAL DEMANDE GLOBAL (toutes les pages) =====
  const modal      = document.getElementById('askModal');
  if (!modal) return;

  const askForm    = modal.querySelector('#askForm');
  const hiddenCar  = modal.querySelector('#askVehicle');
  const startInput = modal.querySelector('#dateStart');
  const endInput   = modal.querySelector('#dateEnd');
  const dateInfo   = modal.querySelector('#dateInfo');
  const waLink     = modal.querySelector('#askWhatsApp');
  const closeBtns  = modal.querySelectorAll('.ask-close, [data-close]');
  const backdrop   = modal.querySelector('.ask-backdrop');

  function formatDate(d) {
    const y  = d.getFullYear();
    const m  = String(d.getMonth() + 1).padStart(2, '0');
    const da = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${da}`;
  }

  function setDefaultDates() {
    if (!startInput || !endInput) return;
    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    const todayStr    = formatDate(today);
    const tomorrowStr = formatDate(tomorrow);

    startInput.min = todayStr;
    endInput.min   = todayStr;
    if (!startInput.value) startInput.value = todayStr;
    if (!endInput.value)   endInput.value   = tomorrowStr;

    if (dateInfo) dateInfo.textContent = '1 jour sélectionné';
  }

  function updateWhatsAppLink(carTitle) {
    if (!waLink) return;
    const msg = encodeURIComponent(
      `Bonjour, je souhaite louer: ${carTitle || ''}\n\nNom: \nDates: du … au …\nTéléphone: `
    );
    waLink.href = `https://wa.me/213656697788?text=${msg}`;
  }

  function openModal(carTitle) {
    if (hiddenCar) hiddenCar.value = carTitle || '';
    setDefaultDates();
    updateWhatsAppLink(carTitle);
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Clic sur tous les boutons "Faire une demande" (home + SUV + marques…)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-ask-car], .btn-ask');
    if (!btn) return;

    e.preventDefault();

    // 1) Priorité au data-ask-car
    let carTitle = btn.getAttribute('data-ask-car') || '';

    // 2) Sinon on récupère data-title sur la carte
    if (!carTitle) {
      const card = btn.closest('.card-veh');
      if (card && card.dataset.title) {
        carTitle = card.dataset.title;
      } else if (card) {
        const modelEl = card.querySelector('.model');
        if (modelEl) carTitle = modelEl.textContent.trim();
      }
    }

    openModal(carTitle || 'Véhicule');
  });

  // Boutons de fermeture
  closeBtns.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      closeModal();
    });
  });

  // Clic sur le backdrop
  if (backdrop) {
    backdrop.addEventListener('click', function () {
      closeModal();
    });
  }

  // Échap pour fermer
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
      closeModal();
    }
  });

  // Boutons rapides +1 / +3 / +7 / +10
  if (askForm && startInput && endInput) {
    askForm.addEventListener('click', function (e) {
      const btn = e.target.closest('.ask-quick-btn');
      if (!btn) return;

      e.preventDefault();

      const days = parseInt(btn.getAttribute('data-duration'), 10);
      if (isNaN(days)) return;

      if (!startInput.value) {
        const today = new Date();
        startInput.value = formatDate(today);
      }

      const base = new Date(startInput.value);
      if (isNaN(base.getTime())) return;

      const end = new Date(base);
      end.setDate(end.getDate() + days);
      endInput.value = formatDate(end);
    });
  }
})();
</script>
<!-- app.js -->
<script src="app.js" defer></script>