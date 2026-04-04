<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeChauffeurMail;
use App\Mail\WelcomeLoueurMail;
use App\Models\Loueur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        return view('front.pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectAfterLogin();
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        return view('front.pages.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'wilaya' => 'required|string|max:100',
            'account_type' => 'required|in:loueur,taxi',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Cet email est déjà utilisé. Si vous avez déjà un compte, connectez-vous.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email n\'est pas valide.',
            'name.required' => 'Le nom est obligatoire.',
            'phone.required' => 'Le téléphone est obligatoire.',
            'company_name.required' => 'Le nom de l\'entreprise est obligatoire.',
            'wilaya.required' => 'La wilaya est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit faire au moins 6 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $isTaxi = $validated['account_type'] === 'taxi';

        try {
            \DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'loueur',
            ]);

            Loueur::create([
                'user_id' => $user->id,
                'account_type' => $validated['account_type'],
                'company_name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'phone' => $validated['phone'],
                'wilaya' => $validated['wilaya'],
                'is_active' => true,
                'offers_transfer' => $isTaxi,
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Registration failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }

        // Send welcome email
        try {
            $this->sendWelcomeEmail($user->loueur);
        } catch (\Exception $e) {
            \Log::warning('Welcome email failed: ' . $e->getMessage());
        }

        Auth::login($user);

        $message = $isTaxi
            ? 'Salam ! Bienvenue sur ResaDZ. Votre espace chauffeur est prêt.'
            : 'Salam ! Bienvenue sur ResaDZ. Votre espace loueur est prêt.';

        $redirectPath = $isTaxi ? '/chauffeur' : '/loueur';
        return redirect($redirectPath)->with('success', $message);
    }

    // Google OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['google' => 'Erreur de connexion Google.']);
        }

        // Find or create user
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // New user - create account
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'role' => 'loueur',
                'email_verified_at' => now(),
            ]);

            // Create loueur profile with minimal info - they'll complete it later
            $loueur = Loueur::create([
                'user_id' => $user->id,
                'company_name' => $googleUser->getName(),
                'slug' => Str::slug($googleUser->getName()) . '-' . Str::random(6),
                'is_active' => true,
            ]);

            // Send welcome email for new Google OAuth accounts
            try {
                $this->sendWelcomeEmail($loueur);
            } catch (\Exception $e) {
                \Log::warning('Welcome email failed (Google): ' . $e->getMessage());
            }
        }

        Auth::login($user, true);

        return $this->redirectAfterLogin();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function sendWelcomeEmail(Loueur $loueur): void
    {
        try {
            $email = $loueur->user->email ?? null;
            if (!$email) {
                return;
            }

            $mailable = $loueur->account_type === 'taxi'
                ? new WelcomeChauffeurMail($loueur)
                : new WelcomeLoueurMail($loueur);

            Mail::to($email)->send($mailable);
        } catch (\Exception $e) {
            Log::warning('Failed to send welcome email to loueur #' . $loueur->id . ': ' . $e->getMessage());
        }
    }

    protected function redirectAfterLogin()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        if ($user->loueur) {
            return redirect('/loueur');
        }

        return redirect()->route('home');
    }
}
