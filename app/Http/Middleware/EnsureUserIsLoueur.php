<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLoueur
{
    /**
     * Vérifie que l'utilisateur connecté est un loueur avec un profil loueur actif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Pas connecté → redirection vers login
        if (!$user) {
            return redirect()->route('filament.loueur.auth.login');
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

        return $next($request);
    }
}
