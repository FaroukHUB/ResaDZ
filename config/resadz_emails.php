<?php

/**
 * ResaDZ — Emails par fonction.
 *
 * Chaque adresse correspond à un type de notification.
 * Sur O2Switch, créer ces boîtes mail dans cPanel puis renseigner le .env :
 *
 *   RESADZ_EMAIL_INSCRIPTION=inscription@resadz.com
 *   RESADZ_EMAIL_RESERVATION=reservation@resadz.com
 *   RESADZ_EMAIL_AVIS=avis@resadz.com
 *   RESADZ_EMAIL_CONTACT=contact@resadz.com
 */

return [

    'inscription' => [
        'address' => env('RESADZ_EMAIL_INSCRIPTION', 'inscription@resadz.com'),
        'name'    => 'ResaDZ — Inscriptions',
    ],

    'reservation' => [
        'address' => env('RESADZ_EMAIL_RESERVATION', 'reservation@resadz.com'),
        'name'    => 'ResaDZ — Réservations',
    ],

    'avis' => [
        'address' => env('RESADZ_EMAIL_AVIS', 'avis@resadz.com'),
        'name'    => 'ResaDZ — Avis',
    ],

    'contact' => [
        'address' => env('RESADZ_EMAIL_CONTACT', 'contact@resadz.com'),
        'name'    => 'ResaDZ — Contact',
    ],

];
