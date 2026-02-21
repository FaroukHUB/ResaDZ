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
