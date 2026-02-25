<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        ]);

        $isTaxi = $validated['account_type'] === 'taxi';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'loueur',
        ]);

        // Create loueur/taxi profile
        Loueur::create([
            'user_id' => $user->id,
            'account_type' => $validated['account_type'],
            'company_name' => $validated['company_name'],
            'slug' => Str::slug($validated['company_name']) . '-' . Str::random(4),
            'phone' => $validated['phone'],
            'wilaya' => $validated['wilaya'],
            'is_active' => true,
            'offers_transfer' => $isTaxi, // Taxis have transfer enabled by default
            'trial_ends_at' => now()->addDays(config('resadz.trial_days', 30)),
        ]);

        Auth::login($user);

        $message = $isTaxi
            ? 'Bienvenue sur ResaDZ ! Votre espace chauffeur est prêt.'
            : 'Bienvenue sur ResaDZ ! Votre espace loueur est prêt.';

        return redirect('/loueur')->with('success', $message);
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
            Loueur::create([
                'user_id' => $user->id,
                'company_name' => $googleUser->getName(),
                'slug' => Str::slug($googleUser->getName()) . '-' . Str::random(4),
                'is_active' => true,
                'trial_ends_at' => now()->addDays(config('resadz.trial_days', 30)),
            ]);
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
