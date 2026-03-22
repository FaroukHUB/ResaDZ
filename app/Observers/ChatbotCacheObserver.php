<?php

namespace App\Observers;

use App\Http\Controllers\Api\ChatbotController;

/**
 * Clears the chatbot knowledge cache whenever a watched model changes.
 * Registered on: Vehicle, Loueur, TransferRoute, ChauffeurVehicle, VehicleOffer, Review
 */
class ChatbotCacheObserver
{
    public function created($model): void
    {
        ChatbotController::clearCache();
    }

    public function updated($model): void
    {
        ChatbotController::clearCache();
    }

    public function deleted($model): void
    {
        ChatbotController::clearCache();
    }
}
