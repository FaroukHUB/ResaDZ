<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLoueur
{
    /**
     * Vérifie que l'utilisateur connecté est un loueur.
     * Les super admins sont redirigés vers le panel admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $request->user();
        } catch (\Exception $e) {
            // Session corrompue ou cache stale — on nettoie et redirige
            $this->cleanupAndRedirect($request);

            return redirect()->route('filament.loueur.auth.login');
        }

        // Pas connecté → redirection vers login
        if (!$user) {
            return redirect()->route('filament.loueur.auth.login');
        }

        // Super Admin → redirection vers panel admin
        if ($user->role === 'super_admin') {
            return redirect()->route('filament.admin.pages.dashboard')
                ->with('info', 'Vous êtes administrateur. Utilisez le panel admin.');
        }

        // Verifie que l'utilisateur a un profil loueur
        $loueur = $user->loueur;

        if (!$loueur) {
            auth()->logout();
            return redirect()->route('filament.loueur.auth.login')
                ->with('error', 'Vous n\'avez pas de compte loueur. Contactez l\'administrateur.');
        }

        // Verifie que c'est bien un loueur (pas un chauffeur/taxi)
        if ($loueur->isTaxi()) {
            return redirect()->route('filament.chauffeur.pages.dashboard')
                ->with('info', 'Vous etes un chauffeur. Redirection vers votre espace.');
        }

        // Verifie que le loueur est actif
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

    protected function cleanupAndRedirect(Request $request): void
    {
        try {
            auth()->logout();
        } catch (\Exception $e) {
            // ignore
        }

        try {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Exception $e) {
            // ignore
        }
    }
}
