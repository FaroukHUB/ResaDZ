<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\MarketingController;
use App\Http\Controllers\Api\PopupController;
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

// Véhicules
Route::prefix('vehicles')->group(function () {
    Route::get('/', [VehicleController::class, 'index']);
    Route::get('/featured', [VehicleController::class, 'featured']);
    Route::get('/category/{slug}', [VehicleController::class, 'byCategory']);
    Route::get('/brand/{slug}', [VehicleController::class, 'byBrand']);
    Route::get('/{slug}', [VehicleController::class, 'show']);
    Route::post('/{slug}/availability', [VehicleController::class, 'checkAvailability']);
});

// Catalogue (marques, catégories, options)
Route::get('/brands', [CatalogController::class, 'brands']);
Route::get('/categories', [CatalogController::class, 'categories']);
Route::get('/options', [CatalogController::class, 'options']);
Route::get('/time-slots', [CatalogController::class, 'timeSlots']);
Route::get('/settings', [CatalogController::class, 'settings']);

// Réservations
Route::prefix('reservations')->group(function () {
    Route::post('/', [ReservationController::class, 'store']);
    Route::get('/{reference}', [ReservationController::class, 'show']);
    Route::post('/{reference}/cancel', [ReservationController::class, 'cancel']);
});

// Popups (tracking)
Route::prefix('popup')->group(function () {
    Route::post('/{popup}/view', [PopupController::class, 'trackView']);
    Route::post('/{popup}/click', [PopupController::class, 'trackClick']);
});

// Marketing
Route::prefix('marketing')->group(function () {
    Route::post('/subscribe', [MarketingController::class, 'subscribe']);
    Route::post('/track-channel', [MarketingController::class, 'trackChannelClick']);
    Route::post('/capture-lead', [MarketingController::class, 'captureLead']);
    Route::post('/apply-referral', [MarketingController::class, 'applyReferral']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/referral-link', [MarketingController::class, 'getReferralLink']);
    });
});

// Analytics & Tracking
Route::prefix('tracking')->group(function () {
    Route::post('/click', [\App\Http\Controllers\Api\TrackingController::class, 'trackClick']);
    Route::get('/realtime', [\App\Http\Controllers\Api\TrackingController::class, 'getRealtimeVisitors']);
    Route::post('/heartbeat', [\App\Http\Controllers\Api\TrackingController::class, 'heartbeat']);
});
