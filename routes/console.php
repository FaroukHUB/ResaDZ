<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate weekly commission invoices every Monday at 6:00 AM
Schedule::command('invoices:generate-weekly')
    ->weeklyOn(1, '06:00') // 1 = Monday
    ->description('Generate weekly commission invoices for loueurs and chauffeurs')
    ->emailOutputOnFailure(config('mail.admin_email'));

// Expire overdue bookings (advance payment deadline passed) - runs every 5 minutes
Schedule::command('bookings:expire-overdue')
    ->everyFiveMinutes()
    ->description('Expire les réservations dont le délai de paiement d\'acompte est dépassé');

// Send review request emails 2 days after booking completion (daily at 10:00 AM)
Schedule::command('reviews:send-requests')
    ->dailyAt('10:00')
    ->description('Send review request emails to clients after completed bookings')
    ->emailOutputOnFailure(config('mail.admin_email'));

// Send booking reminders 1 day before pickup (daily at 9:00 AM)
Schedule::command('bookings:send-reminders')
    ->dailyAt('09:00')
    ->description('Send reminder emails for bookings starting tomorrow')
    ->emailOutputOnFailure(config('mail.admin_email'));

// Purge expired sessions from database (prevents stale session 403 errors)
Schedule::command('session:gc')
    ->hourly()
    ->description('Nettoyer les sessions expirées de la base de données');
