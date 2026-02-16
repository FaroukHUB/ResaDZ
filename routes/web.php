<?php

use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LoueurController;
use App\Http\Controllers\Front\VehicleController;
use Illuminate\Support\Facades\Route;

// Marketplace Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/vehicules', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicule/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/loueur/{slug}', [LoueurController::class, 'show'])->name('loueur.show');
