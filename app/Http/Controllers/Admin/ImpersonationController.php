<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * L'admin se connecte en tant que le loueur (accès complet à son dashboard).
     */
    public function start(Request $request, Loueur $loueur)
    {
        $admin = $request->user();

        // Réservé au super admin
        if (!$admin || !$admin->isSuperAdmin()) {
            abort(403);
        }

        // Le loueur doit avoir un compte utilisateur
        if (!$loueur->user_id || !$loueur->user) {
            return redirect()->back()
                ->with('error', 'Ce loueur n\'a pas de compte utilisateur associé.');
        }

        // Connexion en tant que le loueur
        Auth::loginUsingId($loueur->user_id);
        $request->session()->regenerate();

        // Mémorise l'admin pour pouvoir revenir
        $request->session()->put('impersonator_id', $admin->id);

        // CRUCIAL : AuthenticateSession (panels Filament) compare le hash
        // du mot de passe stocké en session avec celui de l'utilisateur
        // courant, et déconnecte en cas de différence. Après le switch
        // d'utilisateur, il faut donc stocker le hash du loueur.
        $request->session()->put('password_hash_web', $loueur->user->getAuthPassword());

        // Le middleware redirigera vers /chauffeur si c'est un taxi
        return redirect('/loueur');
    }

    /**
     * Retour au compte admin.
     */
    public function stop(Request $request)
    {
        $adminId = $request->session()->get('impersonator_id');

        if (!$adminId) {
            return redirect('/admin');
        }

        $admin = Auth::loginUsingId($adminId);
        $request->session()->forget('impersonator_id');
        $request->session()->regenerate();

        // Même correction qu'au start : stocker le hash du compte admin
        // pour que AuthenticateSession ne déconnecte pas au retour.
        if ($admin) {
            $request->session()->put('password_hash_web', $admin->getAuthPassword());
        }

        return redirect('/admin/loueurs');
    }
}
