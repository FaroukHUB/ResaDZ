<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\JsonResponse;

class PopupController extends Controller
{
    /**
     * Track popup view
     */
    public function trackView(Popup $popup): JsonResponse
    {
        $popup->incrementViews();

        return response()->json(['success' => true]);
    }

    /**
     * Track popup click
     */
    public function trackClick(Popup $popup): JsonResponse
    {
        $popup->incrementClicks();

        return response()->json(['success' => true]);
    }
}
