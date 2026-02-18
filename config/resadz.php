<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Commission Rate
    |--------------------------------------------------------------------------
    |
    | Taux de commission global par défaut (en pourcentage).
    | Peut être modifié par loueur individuellement.
    |
    */
    'commission_rate' => env('RESADZ_COMMISSION_RATE', 5.00),

    /*
    |--------------------------------------------------------------------------
    | Trial Duration
    |--------------------------------------------------------------------------
    |
    | Durée par défaut de la période d'essai (en jours).
    |
    */
    'trial_days' => env('RESADZ_TRIAL_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Commission Currency
    |--------------------------------------------------------------------------
    |
    | Devise pour les commissions.
    |
    */
    'commission_currency' => 'DZD',
];
