<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLoueur
{
    /**
     * Vérifie que l'utilisateur connecté est un loueur OU un super admin.
     * Le super admin a accès à tout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Pas connecté → redirection vers login
        if (!$user) {
            return redirect()->route('filament.loueur.auth.login');
        }

        // Super Admin = accès à tout, sans restriction
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Vérifie que l'utilisateur a un profil loueur
        $loueur = $user->loueur;

        if (!$loueur) {
            auth()->logout();
            return redirect()->route('filament.loueur.auth.login')
                ->with('error', 'Vous n\'avez pas de compte loueur. Contactez l\'administrateur.');
        }

        // Vérifie que le loueur est actif
        if (!$loueur->is_active) {
            auth()->logout();
            return redirect()->route('filament.loueur.auth.login')
                ->with('error', 'Votre compte loueur est désactivé. Contactez l\'administrateur.');
        }

        // Redirige vers l'onboarding si non complété (sauf si déjà sur la page ou requête Livewire/AJAX)
        if (!$loueur->hasCompletedOnboarding()
            && !$request->routeIs('filament.loueur.pages.onboarding')
            && !$request->routeIs('filament.loueur.resources.delivery-zones.*')
            && !$request->is('livewire/*')
            && !$request->ajax()
        ) {
            return redirect()->route('filament.loueur.pages.onboarding');
        }

        return $next($request);
    }
}
