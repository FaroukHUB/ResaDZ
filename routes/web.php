<?php

use App\Http\Controllers\Front\BookingController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\ClientAreaController;
use App\Http\Controllers\Front\GuideController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LoueurController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\SitemapController;
use App\Http\Controllers\Front\TransferController;
use App\Http\Controllers\Front\VehicleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Front\LegalController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
Route::post('/connexion', [AuthController::class, 'login']);
Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
Route::post('/inscription', [AuthController::class, 'register']);
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
Route::get('/deconnexion', [AuthController::class, 'logout'])->name('logout.get');

// Mot de passe oublié
Route::get('/mot-de-passe/oublie', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/mot-de-passe/oublie', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/mot-de-passe/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/mot-de-passe/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Marketplace Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/comment-ca-marche', [HomeController::class, 'commentCaMarche'])->name('comment-ca-marche');
Route::get('/guide', [GuideController::class, 'index'])->name('guide');
Route::get('/vehicules', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicule/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/loueurs', [LoueurController::class, 'index'])->name('loueurs.index');
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

// Pages SEO dédiées par wilaya
Route::get('/location-voiture-alger', function () {
    return view('front.pages.location-voiture-alger');
})->name('seo.location-alger');
Route::get('/location-voiture-aeroport-alger', function () {
    return view('front.pages.location-voiture-aeroport-alger');
})->name('seo.location-aeroport-alger');
Route::get('/location-voiture-oran', function () {
    return view('front.pages.location-voiture-oran');
})->name('seo.location-oran');
Route::get('/location-voiture-constantine', function () {
    return view('front.pages.location-voiture-constantine');
})->name('seo.location-constantine');
Route::get('/location-voiture-blida', function () {
    return view('front.pages.location-voiture-blida');
});
Route::get('/location-voiture-boumerdes', function () {
    return view('front.pages.location-voiture-boumerdes');
});
Route::get('/location-voiture-setif', function () {
    return view('front.pages.location-voiture-setif');
});
Route::get('/location-voiture-sidi-bel-abbes', function () {
    return view('front.pages.location-voiture-sidi-bel-abbes');
});
Route::get('/location-voiture-annaba', function () {
    return view('front.pages.location-voiture-annaba');
})->name('seo.location-annaba');

// Pages SEO par wilaya (wildcard — doit rester APRÈS les routes dédiées)
Route::get('/location-voiture-{wilaya}', [VehicleController::class, 'byWilaya'])->name('vehicles.by-wilaya');

// Comparateur de véhicules
Route::get('/comparer', [VehicleController::class, 'compare'])->name('vehicles.compare');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Pages légales
Route::get('/mentions-legales', [LegalController::class, 'mentionsLegales'])->name('legal.mentions-legales');
Route::get('/conditions-generales-utilisation', [LegalController::class, 'cgu'])->name('legal.cgu');
Route::get('/politique-confidentialite', [LegalController::class, 'confidentialite'])->name('legal.confidentialite');

// Loueur AI description generator
Route::middleware(['auth'])->post('/loueur/generate-description', \App\Http\Controllers\Loueur\GenerateDescriptionController::class)
    ->name('loueur.generate-description');

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

// Stripe Connect (loueur onboarding)
Route::middleware(['auth'])->group(function () {
    Route::get('/stripe/onboard', [\App\Http\Controllers\StripeConnectController::class, 'onboard'])->name('stripe.onboard');
    Route::get('/stripe/onboard/return', [\App\Http\Controllers\StripeConnectController::class, 'onboardReturn'])->name('stripe.onboard.return');
    Route::get('/stripe/dashboard', [\App\Http\Controllers\StripeConnectController::class, 'dashboard'])->name('stripe.dashboard');
});

// Stripe Payment (client checkout)
Route::post('/paiement/{reference}', [\App\Http\Controllers\StripePaymentController::class, 'checkout'])->name('stripe.payment.checkout');
Route::get('/paiement/{reference}/success', [\App\Http\Controllers\StripePaymentController::class, 'success'])->name('stripe.payment.success');

// Stripe Webhook (no auth)
Route::post('/webhook/stripe', [\App\Http\Controllers\StripePaymentController::class, 'webhook'])->name('stripe.webhook');

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

// Admin : accès au dashboard d'un loueur (impersonation)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/impersonate/{loueur}', [\App\Http\Controllers\Admin\ImpersonationController::class, 'start'])
        ->name('admin.impersonate');
    Route::get('/impersonation/stop', [\App\Http\Controllers\Admin\ImpersonationController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

// PWA Manifest (dynamique — utilise le logo ResaDZ depuis Settings)
Route::get('/manifest.json', function () {
    $logoUrl = '/assets/favicon.png'; // fallback
    try {
        $logoSetting = \App\Models\Setting::get('logo_light', '');
        if ($logoSetting) {
            $logoUrl = \Illuminate\Support\Facades\Storage::url($logoSetting);
        }
    } catch (\Exception $e) {}

    return response()->json([
        'name' => \App\Models\Setting::get('company_name', 'ResaDZ'),
        'short_name' => 'ResaDZ',
        'description' => 'Location de voitures entre particuliers en Algérie',
        'start_url' => '/loueur',
        'scope' => '/',
        'display' => 'standalone',
        'background_color' => '#F8FAFF',
        'theme_color' => '#FF6B2C',
        'orientation' => 'portrait',
        'lang' => 'fr',
        'icons' => [
            [
                'src' => $logoUrl,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src' => $logoUrl,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ],
        ],
    ], 200, ['Content-Type' => 'application/manifest+json']);
})->name('manifest.json');
