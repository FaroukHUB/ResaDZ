<?php
// apropos.php

$pageTitle       = 'À propos — MB CARS DZ | Agence de location à Rouiba, Alger';
$pageDescription = 'MB CARS DZ est une agence de location de voitures basée à Rouiba, Alger. Flotte récente, assurance incluse, service personnalisé. Découvrez notre histoire et nos valeurs.';
$canonicalUrl    = 'https://mbcarsdzrouiba.com/Apropos.php';
$base            = './'; // on est à la racine

include __DIR__ . '/partials/header.php';
?>

<style>
/* ===== PAGE À PROPOS — STYLÉE, MAIS SCOPÉE ===== */
.page-about {
  padding: 60px 0 80px;
  color: #f5f5f5;
}

.page-about-inner {
  max-width: 900px;
  margin: 0 auto;
}

/* Titre principal */
.page-about-head {
  text-align: center;
  margin-bottom: 40px;
}

.page-about-kicker {
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.25em;
  color: #d8a63a;
  margin-bottom: 8px;
  font-weight: 700;
}

.page-about-title {
  font-size: 36px;
  font-weight: 900;
  margin: 0 0 12px;
}

.page-about-sub {
  margin: 0;
  font-size: 16px;
  color: #d1d5db;
}

/* Barre dorée */
.page-about-separator {
  margin: 32px auto;
  width: 190px;
  height: 6px;
  border-radius: 999px;
  background: linear-gradient(90deg, #d8a63a, #ffe08a, #d8a63a);
  box-shadow: 0 6px 18px rgba(216,166,58,0.55);
}

/* Blocs texte */
.page-about-section {
  margin-bottom: 40px;
}

.page-about-section h2 {
  font-size: 24px;
  font-weight: 800;
  margin: 0 0 12px;
  color: #facc6b;
}

.page-about-section p {
  margin: 0 0 10px;
  line-height: 1.7;
  color: #e5e7eb;
}

/* Ligne de séparation fine */
.page-about-divider {
  margin: 40px auto;
  width: 100%;
  max-width: 520px;
  height: 1px;
  background: linear-gradient(to right, transparent, #d8a63a, transparent);
  opacity: 0.7;
}

/* ===== CARTES "VALEURS" ===== */
.about-values {
  margin-top: 10px;
}

.about-values-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}

@media (max-width: 900px) {
  .about-values-grid {
    grid-template-columns: 1fr;
  }
}

.about-value-card {
  background: radial-gradient(circle at top left, #1f2933 0, #020617 55%);
  border-radius: 18px;
  padding: 18px 18px 16px;
  border: 1px solid rgba(248, 215, 135, 0.5);
  box-shadow:
    0 18px 40px rgba(0, 0, 0, 0.5),
    0 0 0 1px rgba(0, 0, 0, 0.4) inset;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.about-value-card::before {
  content: "";
  position: absolute;
  inset: auto -40% -2px;
  height: 3px;
  background: linear-gradient(90deg, #ffe08a, #f5c244, #d8a63a);
  opacity: 0.8;
  transform: translateY(3px);
}

/* Petit effet “glow” au survol */
.about-value-card:hover {
  transform: translateY(-4px);
  box-shadow:
    0 24px 55px rgba(0, 0, 0, 0.7),
    0 0 0 1px rgba(248, 215, 135, 0.7) inset;
  border-color: #ffe08a;
}

/* Icônes */
.about-value-icon {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  background: rgba(15, 23, 42, 0.9);
  border: 1px solid rgba(248, 215, 135, 0.5);
  margin-bottom: 10px;
  font-size: 24px;
}

/* Titre + texte */
.about-value-title {
  margin: 0 0 6px;
  font-size: 18px;
  font-weight: 800;
  color: #facc6b;
}

.about-value-text {
  margin: 0;
  font-size: 14px;
  line-height: 1.6;
  color: #e5e7eb;
}

/* Bloc slogan */
.about-slogan {
  text-align: center;
  margin-top: 24px;
}

.about-slogan-quote {
  font-size: 18px;
  font-weight: 800;
  color: #fbbf24;
  margin-bottom: 6px;
}

.about-slogan-sub {
  font-size: 14px;
  color: #d1d5db;
}

/* Animation légère d’apparition */
@keyframes aboutFadeUp {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.page-about-section,
.about-values-grid,
.about-slogan {
  animation: aboutFadeUp 0.6s ease-out;
}

/* Respect des personnes sensibles aux animations */
@media (prefers-reduced-motion: reduce) {
  .page-about-section,
  .about-values-grid,
  .about-slogan {
    animation: none;
  }
}
</style>

<main class="wrap page-about">
  <div class="page-about-inner">

    <!-- Header de page -->
    <header class="page-about-head">
      <p class="page-about-kicker">À propos</p>
      <h1 class="page-about-title">MBCARSDZ — Confiance, Qualité, Mobilité</h1>
      <p class="page-about-sub">
        Une entreprise née sur le terrain, au service de la mobilité en Algérie.
      </p>
      <div class="page-about-separator"></div>
    </header>

    <!-- Notre histoire -->
    <section class="page-about-section">
      <h2>Notre histoire</h2>
      <p>
        MBCARSDZ est née d’une idée simple : faciliter la mobilité en Algérie en offrant
        un service de location de véhicules fiable, transparent et accessible à tous.
      </p>
      <p>
        Au départ, il ne s’agissait que d’un petit projet, animé par la passion de
        l’automobile et l’envie d’aider les voyageurs à se déplacer en toute sérénité.
      </p>
      <p>
        Très vite, les premiers clients ont parlé de leur expérience, saluant notre
        sérieux, notre disponibilité et la qualité de nos véhicules.
      </p>
      <p>
        De fil en aiguille, ce qui avait commencé comme une petite activité s’est
        transformé en une véritable entreprise.
      </p>
      <p>
        MBCARSDZ s’est construite sur le terrain, au contact direct avec ses clients :
        familles en voyage, professionnels en déplacement, touristes, étudiants, ou
        personnes ayant simplement besoin d’un véhicule ponctuellement.
      </p>
      <p>
        Chaque demande, chaque retour, chaque expérience a contribué à façonner notre
        identité et renforcer notre vision : créer un service proche des gens, humain,
        flexible et moderne.
      </p>
      <p>
        Aujourd’hui, MBCARSDZ continue de grandir avec la même ambition qu’au premier
        jour : accompagner nos clients partout où ils vont, avec des solutions de
        mobilité adaptées, sûres et pensées pour leur confort.
      </p>
    </section>

    <div class="page-about-divider"></div>

    <!-- Nos valeurs -->
    <section class="page-about-section">
      <h2>Nos valeurs</h2>

      <div class="about-values">
        <div class="about-values-grid">

          <!-- Confiance -->
          <article class="about-value-card">
            <div class="about-value-icon">🤝</div>
            <h3 class="about-value-title">Confiance</h3>
            <p class="about-value-text">
              La confiance est la base de notre relation avec chaque client.
              Nous garantissons une transparence totale dans nos tarifs, nos
              engagements et nos véhicules.
            </p>
            <p class="about-value-text">
              Chaque voiture mise en location est contrôlée, entretenue et suivie
              pour assurer une expérience sereine, sans mauvaise surprise.
            </p>
          </article>

          <!-- Qualité -->
          <article class="about-value-card">
            <div class="about-value-icon">✨</div>
            <h3 class="about-value-title">Qualité</h3>
            <p class="about-value-text">
              Nous mettons un point d’honneur à proposer des véhicules propres,
              modernes et parfaitement entretenus.
            </p>
            <p class="about-value-text">
              La qualité, c’est aussi notre service : un accompagnement
              professionnel, une communication claire et une disponibilité réelle
              pour répondre aux besoins de nos clients, du premier contact jusqu’au
              retour du véhicule.
            </p>
          </article>

          <!-- Mobilité -->
          <article class="about-value-card">
            <div class="about-value-icon">🚗</div>
            <h3 class="about-value-title">Mobilité</h3>
            <p class="about-value-text">
              Notre mission est de faciliter vos déplacements, quels qu’ils soient.
            </p>
            <p class="about-value-text">
              Que ce soit pour une sortie rapide, un voyage familial, un déplacement
              professionnel ou une occasion spéciale, nous proposons des solutions
              adaptées à chaque situation. Avec MBCARSDZ, vous roulez librement et
              l’esprit tranquille.
            </p>
          </article>

        </div>
      </div>
    </section>

    <div class="page-about-divider"></div>

    <!-- Notre engagement -->
    <section class="page-about-section">
      <h2>Notre engagement</h2>
      <p>
        MBCARSDZ s’engage à offrir une expérience simple, rapide et fiable.
      </p>
      <p>
        Réserver un véhicule ne doit jamais être une source de stress.
      </p>
      <p>
        C’est pourquoi nous avons mis en place un service flexible, réactif et en
        constante amélioration.
      </p>
      <p>
        Nous écoutons nos clients, nous comprenons leurs besoins et nous travaillons
        chaque jour à dépasser leurs attentes.
      </p>
    </section>

    <div class="page-about-divider"></div>

    <!-- Slogan -->
    <section class="page-about-section about-slogan">
      <p class="about-slogan-quote">
        « MBCARSDZ — Confiance, Qualité, Mobilité. »
      </p>
      <p class="about-slogan-sub">
        Un slogan qui résume notre identité, nos valeurs et notre vision.
      </p>
    </section>

  </div>
</main>

<?php
include __DIR__ . '/partials/footer.php';
?>
