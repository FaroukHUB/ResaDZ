<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('front.pages.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // On ne révèle pas si l'email existe ou non (sécurité)
            return back()->with('status', 'Si cette adresse existe dans notre système, vous recevrez un email avec un lien de réinitialisation.');
        }

        // Générer un token
        $token = Str::random(64);

        // Supprimer les anciens tokens pour cet email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insérer le nouveau token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Envoyer l'email
        try {
            Mail::send('emails.reset-password', [
                'token' => $token,
                'email' => $request->email,
                'userName' => $user->name,
            ], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Réinitialisation de votre mot de passe — ResaDZ');
            });
        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Impossible d\'envoyer l\'email. Veuillez réessayer plus tard ou nous contacter sur WhatsApp.']);
        }

        return back()->with('status', 'Si cette adresse existe dans notre système, vous recevrez un email avec un lien de réinitialisation.');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('front.pages.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifier le token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation est invalide.']);
        }

        // Vérifier que le token n'a pas expiré (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Ce lien a expiré. Veuillez refaire une demande.']);
        }

        // Vérifier le hash du token
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation est invalide.']);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Aucun compte trouvé avec cette adresse.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.');
    }
}
