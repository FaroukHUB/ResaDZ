<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:50',
        ]);

        // Check if already subscribed
        $existing = NewsletterSubscriber::where('email', $request->email)->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return response()->json([
                    'success' => true,
                    'message' => 'Vous êtes déjà inscrit',
                ]);
            }

            // Reactivate
            $existing->update([
                'status' => 'active',
                'confirmed_at' => now(),
                'unsubscribed_at' => null,
            ]);
        } else {
            NewsletterSubscriber::create([
                'email' => $request->email,
                'name' => $request->name,
                'source' => $request->source ?? 'api',
                'status' => 'active',
                'confirmed_at' => now(),
                'ip_address' => $request->ip(),
            ]);
        }

        // Also create a lead
        Lead::create([
            'email' => $request->email,
            'name' => $request->name,
            'source' => $request->source ?? 'newsletter',
            'subscribed_newsletter' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
        ]);
    }

    /**
     * Track social channel click
     */
    public function trackChannelClick(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:whatsapp,telegram,facebook,instagram',
            'source' => 'nullable|string|max:50',
        ]);

        Lead::create([
            'source' => $request->type . '_cta',
            'source_page' => $request->header('Referer'),
            'subscribed_' . $request->type => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'utm_source' => $request->source,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Capture lead from form
     */
    public function captureLead(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'name' => 'nullable|string|max:255',
            'source' => 'required|string|max:50',
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou téléphone requis',
            ], 422);
        }

        Lead::capture($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Merci !',
        ]);
    }

    /**
     * Get referral link for authenticated user
     */
    public function getReferralLink(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        // Generate referral code if not exists
        if (!$user->referral_code) {
            $user->referral_code = $this->generateReferralCode();
            $user->save();
        }

        return response()->json([
            'success' => true,
            'referral_code' => $user->referral_code,
            'referral_link' => route('home', ['ref' => $user->referral_code]),
            'referral_count' => $user->referral_count,
            'referral_credits' => $user->referral_credits,
        ]);
    }

    /**
     * Apply referral code during registration
     */
    public function applyReferral(Request $request): JsonResponse
    {
        $request->validate([
            'referral_code' => 'required|string|size:8',
        ]);

        $referrer = User::where('referral_code', strtoupper($request->referral_code))->first();

        if (!$referrer) {
            return response()->json([
                'success' => false,
                'message' => 'Code de parrainage invalide',
            ], 404);
        }

        // Store in session for use during registration
        session(['referral_code' => $referrer->referral_code]);

        return response()->json([
            'success' => true,
            'referrer_name' => $referrer->name,
            'message' => 'Code appliqué !',
        ]);
    }

    /**
     * Generate unique referral code
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
