<?php

use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Front\BookingController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\ClientAreaController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LoueurController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\SitemapController;
use App\Http\Controllers\Front\TransferController;
use App\Http\Controllers\Front\VehicleController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
Route::post('/connexion', [AuthController::class, 'login']);
Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
Route::post('/inscription', [AuthController::class, 'register']);
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Marketplace Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/comment-ca-marche', [HomeController::class, 'commentCaMarche'])->name('comment-ca-marche');
Route::get('/vehicules', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicule/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/loueur/{slug}', [LoueurController::class, 'show'])->name('loueur.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Réservation
Route::get('/reserver/{slug}', [BookingController::class, 'create'])->name('booking.create');
Route::post('/reserver/calculer', [BookingController::class, 'calculatePrice'])->name('booking.calculate');
Route::post('/reserver/options', [BookingController::class, 'checkOptions'])->name('booking.check-options');
Route::post('/reserver', [BookingController::class, 'store'])->name('booking.store');
Route::get('/reservation/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

// Confirmation client (lien unique envoyé par email)
Route::get('/ma-reservation/{token}', [BookingController::class, 'clientConfirmation'])->name('booking.client-confirmation');
Route::post('/ma-reservation/{token}/documents', [BookingController::class, 'uploadDocuments'])->name('booking.upload-documents');

// Espace client (dashboard + messagerie)
Route::prefix('espace-client/{token}')->group(function () {
    Route::get('/', [ClientAreaController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/messages', [ClientAreaController::class, 'conversation'])->name('client.conversation');
    Route::post('/messages', [ClientAreaController::class, 'sendMessage'])->name('client.send-message');
    Route::get('/messages/refresh', [ClientAreaController::class, 'refreshMessages'])->name('client.refresh-messages');
    // Support
    Route::get('/support', [ClientAreaController::class, 'support'])->name('client.support');
    Route::post('/support', [ClientAreaController::class, 'createSupportTicket'])->name('client.support.create');
    Route::get('/support/{conversation}', [ClientAreaController::class, 'showSupportConversation'])->name('client.support.show');
    Route::post('/support/{conversation}', [ClientAreaController::class, 'replySupportConversation'])->name('client.support.reply');
});

// Avis clients
Route::get('/avis/{token}', [ReviewController::class, 'create'])->name('review.create');
Route::post('/avis/{token}', [ReviewController::class, 'store'])->name('review.store');

// Pages SEO par wilaya
Route::get('/location-voiture-{wilaya}', [VehicleController::class, 'byWilaya'])->name('vehicles.by-wilaya');

// Comparateur de véhicules
Route::get('/comparer', [VehicleController::class, 'compare'])->name('vehicles.compare');

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

// Contract PDF via token (for client confirmation page - no auth required)
Route::get('/contrat/client/{token}/telecharger', [\App\Http\Controllers\Loueur\ContractController::class, 'downloadByToken'])
    ->name('contract.download-by-token');

// Boost Payments (requires auth)
Route::middleware(['auth'])->prefix('boost')->group(function () {
    Route::get('/paypal/{boost}', [\App\Http\Controllers\BoostPaymentController::class, 'createPayPalPayment'])
        ->name('boost.paypal.create');
    Route::get('/paypal/success', [\App\Http\Controllers\BoostPaymentController::class, 'paypalSuccess'])
        ->name('boost.paypal.success');
    Route::get('/paypal/cancel', [\App\Http\Controllers\BoostPaymentController::class, 'paypalCancel'])
        ->name('boost.paypal.cancel');
});

// PayPal Webhook (no auth - called by PayPal)
Route::post('/webhook/paypal', [\App\Http\Controllers\BoostPaymentController::class, 'paypalWebhook'])
    ->name('boost.paypal.webhook');

// Transferts / Taxi
Route::get('/transferts', [TransferController::class, 'search'])->name('transfers.search');
Route::post('/transferts/reserver', [TransferController::class, 'book'])->name('transfers.book');
Route::get('/transfert/{reference}', [TransferController::class, 'confirmation'])->name('transfers.confirmation');

// Admin Invoice PDF
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\Admin\InvoicePdfController::class, 'download'])
        ->name('admin.invoices.pdf');
    Route::get('/invoices/{invoice}/pdf/view', [\App\Http\Controllers\Admin\InvoicePdfController::class, 'stream'])
        ->name('admin.invoices.pdf.view');
});
