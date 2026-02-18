<?php

use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Front\BookingController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LoueurController;
use App\Http\Controllers\Front\SitemapController;
use App\Http\Controllers\Front\VehicleController;
use Illuminate\Support\Facades\Route;

// Marketplace Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/vehicules', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicule/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/loueur/{slug}', [LoueurController::class, 'show'])->name('loueur.show');

// Réservation
Route::get('/reserver/{slug}', [BookingController::class, 'create'])->name('booking.create');
Route::post('/reserver/calculer', [BookingController::class, 'calculatePrice'])->name('booking.calculate');
Route::post('/reserver', [BookingController::class, 'store'])->name('booking.store');
Route::get('/reservation/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Calendar iCal Feed (for Google Calendar sync)
Route::get('/calendar/ical/{token}.ics', [CalendarController::class, 'icalFeed'])->name('calendar.ical');

// Contract PDF (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/contrat/{booking}/telecharger', [\App\Http\Controllers\Loueur\ContractController::class, 'download'])
        ->name('contract.download');
    Route::get('/contrat/{booking}/apercu', [\App\Http\Controllers\Loueur\ContractController::class, 'preview'])
        ->name('contract.preview');
});
