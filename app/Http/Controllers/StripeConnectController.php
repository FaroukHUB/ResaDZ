<?php

namespace App\Http\Controllers;

use App\Models\Loueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeConnectController extends Controller
{
    /**
     * Create a Stripe Connect Express account for the loueur and redirect to onboarding.
     */
    public function onboard()
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) abort(403);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create account if not exists
            if (!$loueur->stripe_account_id) {
                $account = \Stripe\Account::create([
                    'type' => 'express',
                    'country' => 'FR', // Comptes EUR pour la diaspora
                    'email' => Auth::user()->email,
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                    'business_profile' => [
                        'name' => $loueur->company_name,
                        'mcc' => '7512', // Automobile rental
                        'url' => url('/loueur/' . $loueur->slug),
                    ],
                ]);

                $loueur->update(['stripe_account_id' => $account->id]);
            }

            // Create onboarding link
            $link = \Stripe\AccountLink::create([
                'account' => $loueur->stripe_account_id,
                'refresh_url' => route('stripe.onboard'),
                'return_url' => route('stripe.onboard.return'),
                'type' => 'account_onboarding',
            ]);

            return redirect($link->url);

        } catch (\Exception $e) {
            Log::error('Stripe Connect onboarding error: ' . $e->getMessage());
            return redirect()->route('filament.loueur.pages.dashboard')
                ->with('error', 'Erreur lors de la connexion Stripe. Réessayez.');
        }
    }

    /**
     * Return from Stripe onboarding.
     */
    public function onboardReturn()
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur || !$loueur->stripe_account_id) {
            return redirect()->route('filament.loueur.pages.dashboard');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $account = \Stripe\Account::retrieve($loueur->stripe_account_id);

            if ($account->charges_enabled && $account->payouts_enabled) {
                $loueur->update(['stripe_onboarding_complete' => true]);
                return redirect()->route('filament.loueur.pages.dashboard')
                    ->with('success', 'Votre compte Stripe est connecté ! Vous pouvez recevoir des paiements en ligne.');
            }

            return redirect()->route('filament.loueur.pages.dashboard')
                ->with('warning', 'Votre compte Stripe est en cours de vérification. Vous pourrez recevoir des paiements une fois la vérification terminée.');

        } catch (\Exception $e) {
            Log::error('Stripe onboard return error: ' . $e->getMessage());
            return redirect()->route('filament.loueur.pages.dashboard')
                ->with('error', 'Erreur de vérification Stripe.');
        }
    }

    /**
     * Stripe Connect dashboard link for loueur.
     */
    public function dashboard()
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur || !$loueur->stripe_account_id) abort(403);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $link = \Stripe\Account::createLoginLink($loueur->stripe_account_id);
            return redirect($link->url);
        } catch (\Exception $e) {
            return redirect()->route('filament.loueur.pages.dashboard')
                ->with('error', 'Impossible d\'accéder au dashboard Stripe.');
        }
    }
}
