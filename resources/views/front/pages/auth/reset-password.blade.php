@extends('front.layouts.app')

@section('title', 'Nouveau mot de passe - ResaDZ')

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
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Nouveau mot de passe</h2>
            <p class="mt-2 text-gray-500">Choisissez un nouveau mot de passe pour votre compte.</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            <div>
                <label for="email_display" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email_display" value="{{ $email ?? old('email') }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-gray-900"
                    placeholder="Minimum 8 caractères">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-gray-900"
                    placeholder="Retapez votre mot de passe">
            </div>

            <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg">
                Réinitialiser mon mot de passe
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
