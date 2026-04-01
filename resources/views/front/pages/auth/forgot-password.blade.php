@extends('front.layouts.app')

@section('title', 'Mot de passe oublié - ResaDZ')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-6">
        <!-- Header -->
        <div class="text-center">
            @php $logoLight = \App\Models\Setting::get('logo_light', ''); $siteName = \App\Models\Setting::get('company_name', 'ResaDZ'); @endphp
            <a href="{{ route('home') }}">
                @if($logoLight)
                    <img src="{{ Storage::url($logoLight) }}" alt="{{ $siteName }}" class="h-16 w-auto mx-auto">
                @else
                    <span class="text-3xl font-black text-gray-900">{{ $siteName }}</span>
                @endif
            </a>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Mot de passe oublié</h2>
            <p class="mt-2 text-gray-500">Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
        </div>

        <!-- Success Message -->
        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-gray-900"
                    placeholder="votre@email.com">
            </div>

            <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg">
                Envoyer le lien de réinitialisation
            </button>
        </form>

        <!-- Back to login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à la connexion
            </a>
        </div>
    </div>
</div>
@endsection
