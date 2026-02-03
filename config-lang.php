<?php
// ===============================================
// config-lang.php
// Gestion simple des langues (fr / ar / en)
// ===============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Langues supportées et langue par défaut
 */
const SUPPORTED_LANGS = ['fr', 'ar', 'en'];
const DEFAULT_LANG    = 'fr';

/**
 * 1) Récupérer la langue demandée dans l'URL (?lang=fr)
 * 2) Sinon, reprendre celle stockée en session
 * 3) Sinon, utiliser la langue par défaut
 */
$lang = DEFAULT_LANG;

// via ?lang=xx
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS, true)) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
}
// via session
elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], SUPPORTED_LANGS, true)) {
    $lang = $_SESSION['lang'];
}

/**
 * Charger le fichier de traduction correspondant
 * /lang/fr.php, /lang/ar.php, /lang/en.php
 */
$langFile = __DIR__ . '/lang/' . $lang . '.php';

if (!is_file($langFile)) {
    // fallback sécurité
    $lang     = DEFAULT_LANG;
    $langFile = __DIR__ . '/lang/' . $lang . '.php';
}

$T = require $langFile;

/**
 * Helper de traduction
 *
 * t('menu_home')
 * t('menu_home', 'Accueil')  // fallback si la clé manque
 */
function t(string $key, ?string $fallback = null): string
{
    global $T;

    if (isset($T[$key])) {
        return $T[$key];
    }

    return $fallback ?? $key;
}

/**
 * Helper pour la direction (utile pour <html dir="..."> ou des classes)
 * lang_dir() => 'rtl' pour ar, 'ltr' pour le reste
 */
function lang_dir(): string
{
    // on réutilise la variable globale $lang
    global $lang;
    return $lang === 'ar' ? 'rtl' : 'ltr';
}

/**
 * Helper pour récupérer la langue courante si besoin
 */
function current_lang(): string
{
    global $lang;
    return $lang;
}
