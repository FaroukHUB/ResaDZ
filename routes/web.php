<?php

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
