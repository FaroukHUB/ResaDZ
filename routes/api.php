<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\MarketingController;
use App\Http\Controllers\Api\PopupController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - ResaDZ
|--------------------------------------------------------------------------
|
| Ces routes sont accessibles via /api/...
| Elles fournissent les données pour le front-end.
|
*/

// Véhicules - Public read endpoints with generous rate limit
Route::middleware('throttle:public')->prefix('vehicles')->group(function () {
    Route::get('/', [VehicleController::class, 'index']);
    Route::get('/featured', [VehicleController::class, 'featured']);
    Route::get('/category/{slug}', [VehicleController::class, 'byCategory']);
    Route::get('/brand/{slug}', [VehicleController::class, 'byBrand']);
    Route::get('/{slug}', [VehicleController::class, 'show']);
    Route::post('/{slug}/availability', [VehicleController::class, 'checkAvailability']);
    Route::get('/{slug}/unavailable-dates', [VehicleController::class, 'unavailableDates']);
});

// Catalogue (marques, catégories, options) - Public read endpoints
Route::middleware('throttle:public')->group(function () {
    Route::get('/brands', [CatalogController::class, 'brands']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/options', [CatalogController::class, 'options']);
    Route::get('/time-slots', [CatalogController::class, 'timeSlots']);
    Route::get('/settings', [CatalogController::class, 'settings']);
});

// Réservations - Booking rate limit
Route::middleware('throttle:booking')->prefix('reservations')->group(function () {
    Route::post('/', [ReservationController::class, 'store']);
    Route::get('/{reference}', [ReservationController::class, 'show']);
    Route::post('/{reference}/cancel', [ReservationController::class, 'cancel']);
});

// Popups (tracking) - Tracking rate limit
Route::middleware('throttle:tracking')->prefix('popup')->group(function () {
    Route::post('/{popup}/view', [PopupController::class, 'trackView']);
    Route::post('/{popup}/click', [PopupController::class, 'trackClick']);
});

// Marketing - Strict rate limit for spam prevention
Route::middleware('throttle:marketing')->prefix('marketing')->group(function () {
    Route::post('/subscribe', [MarketingController::class, 'subscribe']);
    Route::post('/track-channel', [MarketingController::class, 'trackChannelClick']);
    Route::post('/capture-lead', [MarketingController::class, 'captureLead']);
    Route::post('/apply-referral', [MarketingController::class, 'applyReferral']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/referral-link', [MarketingController::class, 'getReferralLink']);
    });
});

// Analytics & Tracking - Higher rate limit for analytics
Route::middleware('throttle:tracking')->prefix('tracking')->group(function () {
    Route::post('/click', [\App\Http\Controllers\Api\TrackingController::class, 'trackClick']);
    Route::get('/realtime', [\App\Http\Controllers\Api\TrackingController::class, 'getRealtimeVisitors']);
    Route::post('/heartbeat', [\App\Http\Controllers\Api\TrackingController::class, 'heartbeat']);
});

// Map - Véhicules par wilaya pour Leaflet.js
Route::middleware('throttle:public')->get('/map/vehicles-by-wilaya', [\App\Http\Controllers\Api\MapController::class, 'vehiclesByWilaya']);

// Chatbot - Rate limited to prevent abuse
Route::middleware('throttle:marketing')->post('/chatbot', [ChatbotController::class, 'chat']);

// Panel Assistant (Résabot) - Rate limited
Route::middleware('throttle:marketing')->post('/panel-assistant', [\App\Http\Controllers\Api\PanelAssistantController::class, 'chat']);

// Push Notifications - Sensitive rate limit
Route::middleware('throttle:sensitive')->prefix('push')->group(function () {
    Route::get('/public-key', [PushSubscriptionController::class, 'publicKey']);
    Route::middleware('web')->group(function () {
        Route::post('/subscribe', [PushSubscriptionController::class, 'store']);
        Route::post('/unsubscribe', [PushSubscriptionController::class, 'destroy']);
    });
});
