<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate monthly commission invoices on the 1st of each month at 6:00 AM
Schedule::command('invoices:generate-monthly')
    ->monthlyOn(1, '06:00')
    ->description('Generate monthly commission invoices for loueurs')
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
