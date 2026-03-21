<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsChauffeur
{
    /**
     * Verifie que l'utilisateur connecte est un chauffeur (account_type = taxi) OU un super admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Pas connecte -> redirection vers login
        if (!$user) {
            return redirect()->route('filament.chauffeur.auth.login');
        }

        // Super Admin = acces a tout, sans restriction
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Verifie que l'utilisateur a un profil loueur de type taxi
        $loueur = $user->loueur;

        if (!$loueur) {
            auth()->logout();
            return redirect()->route('filament.chauffeur.auth.login')
                ->with('error', 'Vous n\'avez pas de compte chauffeur. Contactez l\'administrateur.');
        }

        // Verifie que c'est bien un chauffeur/taxi
        if (!$loueur->isTaxi()) {
            auth()->logout();
            return redirect()->route('filament.chauffeur.auth.login')
                ->with('error', 'Ce portail est reserve aux chauffeurs. Utilisez /loueur pour les locations.');
        }

        // Verifie que le chauffeur est actif
        if (!$loueur->is_active) {
            auth()->logout();
            return redirect()->route('filament.chauffeur.auth.login')
                ->with('error', 'Votre compte chauffeur est desactive. Contactez l\'administrateur.');
        }

        // Redirige vers l'onboarding si non complete
        if (!$loueur->hasCompletedOnboarding()
            && !$request->routeIs('filament.chauffeur.pages.onboarding')
            && !$request->routeIs('filament.chauffeur.resources.delivery-zones.*')
            && !$request->is('livewire/*')
            && !$request->ajax()
        ) {
            return redirect()->route('filament.chauffeur.pages.onboarding');
        }

        return $next($request);
    }
}
