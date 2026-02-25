@extends('front.layouts.app')

@section('title', 'Créer un compte - ResaDZ')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-lg space-y-6">
        <!-- Header -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="text-3xl font-black text-gray-900">Resa<span class="text-red-600">DZ</span></a>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Rejoindre ResaDZ</h2>
            <p class="mt-2 text-gray-500">Créez votre compte en 2 minutes</p>
        </div>

        <!-- Google Register -->
        <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            S'inscrire avec Google
        </a>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-gray-50 px-4 text-gray-400">ou remplissez le formulaire</span>
            </div>
        </div>

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Account Type Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Je suis</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="account_type" value="loueur" class="peer sr-only" {{ old('account_type', 'loueur') === 'loueur' ? 'checked' : '' }} required>
                        <div class="flex flex-col items-center gap-2 p-4 border-2 border-gray-200 rounded-xl peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-gray-300 transition">
                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0H21M3.375 14.25h17.25M21 12.75V14.25M3.375 14.25V5.625m0 0A1.125 1.125 0 0 1 4.5 4.5h9.375"/></svg>
                            <span class="text-sm font-semibold text-gray-700">Loueur</span>
                            <span class="text-xs text-gray-400 text-center">Location de véhicules</span>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="account_type" value="taxi" class="peer sr-only" {{ old('account_type') === 'taxi' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center gap-2 p-4 border-2 border-gray-200 rounded-xl peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-gray-300 transition">
                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span class="text-sm font-semibold text-gray-700">Taxi / VTC</span>
                            <span class="text-xs text-gray-400 text-center">Transfert & livraison</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Votre nom</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="Ahmed Bensalem">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="0555 XX XX XX">
                </div>
            </div>

            <div>
                <label for="company_name" id="company_label" class="block text-sm font-medium text-gray-700 mb-1">Nom de votre agence / entreprise</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                    placeholder="Ex: AutoLoc Alger">
            </div>

            <div>
                <label for="wilaya" class="block text-sm font-medium text-gray-700 mb-1">Wilaya</label>
                <select id="wilaya" name="wilaya" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-gray-900">
                    <option value="">Sélectionnez votre wilaya</option>
                    @php
                        $wilayas = ['Adrar','Chlef','Laghouat','Oum El Bouaghi','Batna','Béjaïa','Biskra','Béchar','Blida','Bouira','Tamanrasset','Tébessa','Tlemcen','Tiaret','Tizi Ouzou','Alger','Djelfa','Jijel','Sétif','Saïda','Skikda','Sidi Bel Abbès','Annaba','Guelma','Constantine','Médéa','Mostaganem','M\'Sila','Mascara','Ouargla','Oran','El Bayadh','Illizi','Bordj Bou Arréridj','Boumerdès','El Tarf','Tindouf','Tissemsilt','El Oued','Khenchela','Souk Ahras','Tipaza','Mila','Aïn Defla','Naâma','Aïn Témouchent','Ghardaïa','Relizane','Timimoun','Bordj Badji Mokhtar','Ouled Djellal','Béni Abbès','In Salah','In Guezzam','Touggourt','Djanet','El M\'Ghair','El Meniaa'];
                    @endphp
                    @foreach($wilayas as $index => $wilaya)
                        <option value="{{ $wilaya }}" {{ old('wilaya') === $wilaya ? 'selected' : '' }}>
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} - {{ $wilaya }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                    placeholder="votre@email.com">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="Min. 6 caractères">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="Même mot de passe">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-600/20 text-lg">
                Créer mon compte gratuitement
            </button>

            <p class="text-xs text-gray-400 text-center">
                1 mois d'essai gratuit. Aucun paiement requis.
            </p>
        </form>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-gray-500">Déjà un compte ?
                <a href="{{ route('login') }}" class="text-red-600 font-semibold hover:text-red-700">Se connecter</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="account_type"]');
    const label = document.getElementById('company_label');
    const input = document.getElementById('company_name');
    const submitBtn = document.querySelector('button[type="submit"]');

    function updateForm(type) {
        if (type === 'taxi') {
            label.textContent = 'Votre nom commercial';
            input.placeholder = 'Ex: Taxi Mohamed Alger';
            submitBtn.textContent = 'Créer mon compte chauffeur';
        } else {
            label.textContent = 'Nom de votre agence / entreprise';
            input.placeholder = 'Ex: AutoLoc Alger';
            submitBtn.textContent = 'Créer mon compte gratuitement';
        }
    }

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() { updateForm(this.value); });
    });

    // Init
    const checked = document.querySelector('input[name="account_type"]:checked');
    if (checked) updateForm(checked.value);
});
</script>
@endpush
