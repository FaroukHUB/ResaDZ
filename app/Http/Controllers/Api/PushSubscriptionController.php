<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Get VAPID public key for client subscription.
     */
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => WebPushService::getPublicKey(),
        ]);
    }

    /**
     * Store a new push subscription for the authenticated loueur.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user || !$user->loueur) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        $loueur = $user->loueur;

        // Update or create subscription
        PushSubscription::updateOrCreate(
            [
                'loueur_id' => $loueur->id,
                'endpoint' => $request->input('endpoint'),
            ],
            [
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Notifications activées']);
    }

    /**
     * Remove a push subscription.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user || !$user->loueur) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        $loueur = $user->loueur;

        PushSubscription::where('loueur_id', $loueur->id)
            ->where('endpoint', $request->input('endpoint'))
            ->delete();

        return response()->json(['success' => true, 'message' => 'Notifications désactivées']);
    }
}
