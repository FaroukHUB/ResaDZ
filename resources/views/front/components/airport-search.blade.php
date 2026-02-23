{{-- Section Recherche Aéroport Premium --}}
<section class="relative">
    {{-- Hero Image avec chevauchement --}}
    <div class="relative h-[300px] lg:h-[400px]">
        @if(file_exists(public_path('assets/airport-hero.jpg')))
            <img src="{{ asset('assets/airport-hero.jpg') }}" alt="Location voiture aéroport Algérie" class="w-full h-full object-cover">
        @else
            {{-- Fallback gradient --}}
            <div class="w-full h-full bg-gradient-to-br from-slate-800 via-slate-900 to-black"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-transparent"></div>

        {{-- Title --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white text-center px-4 drop-shadow-lg">
                Besoin d'une voiture à votre arrivée ?
            </h2>
        </div>
    </div>

    {{-- Form Container with negative margin to overlap --}}
    <div class="relative bg-[#1a1a1a] pb-16 lg:pb-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-16 lg:-mt-20">
                {{-- Form Card --}}
                <form action="{{ route('vehicles.index') }}" method="GET" class="bg-[#252525] rounded-2xl p-6 lg:p-10 shadow-2xl border border-gray-800">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Date d'arrivée --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Date d'arrivée</label>
                            <input type="date"
                                   name="pickup_date"
                                   value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-4 bg-[#1a1a1a] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                        </div>

                        {{-- Heure d'arrivée --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Heure d'arrivée</label>
                            <select name="pickup_time"
                                    class="w-full px-4 py-4 bg-[#1a1a1a] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors appearance-none cursor-pointer">
                                <option value="">Sélectionner l'heure</option>
                                @for($h = 0; $h < 24; $h++)
                                    <option value="{{ sprintf('%02d:00', $h) }}" {{ $h == 10 ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Date de retour --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Date de retour</label>
                            <input type="date"
                                   name="return_date"
                                   value="{{ date('Y-m-d', strtotime('+5 days')) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-4 bg-[#1a1a1a] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                        </div>

                        {{-- Aéroport --}}
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Aéroport</label>
                            <select name="wilaya"
                                    class="w-full px-4 py-4 bg-[#1a1a1a] border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors appearance-none cursor-pointer">
                                <option value="">Choisir un aéroport</option>
                                <option value="Alger">Alger - Houari Boumediene</option>
                                <option value="Oran">Oran - Ahmed Ben Bella</option>
                                <option value="Constantine">Constantine - Mohamed Boudiaf</option>
                                <option value="Annaba">Annaba - Rabah Bitat</option>
                                <option value="Tlemcen">Tlemcen - Zenata</option>
                                <option value="Sétif">Sétif - 8 Mai 1945</option>
                                <option value="Béjaïa">Béjaïa - Abane Ramdane</option>
                                <option value="Batna">Batna - Mostefa Ben Boulaid</option>
                                <option value="Biskra">Biskra - Mohamed Khider</option>
                                <option value="Chlef">Chlef - Aboubakr Belkaid</option>
                                <option value="Ghardaïa">Ghardaïa - Noumérat</option>
                                <option value="El Oued">El Oued - Guemar</option>
                                <option value="Tébessa">Tébessa - Cheikh Larbi Tebessi</option>
                                <option value="Jijel">Jijel - Ferhat Abbas</option>
                                <option value="Tamanrasset">Tamanrasset - Aguenar</option>
                                <option value="Adrar">Adrar - Touat Cheikh Sidi Mohamed Belkebir</option>
                                <option value="Béchar">Béchar - Boudghene Ben Ali Lotfi</option>
                                <option value="Illizi">Illizi - Takhamalt</option>
                                <option value="Ouargla">Ouargla - Ain Beida</option>
                                <option value="Djanet">Djanet - Tiska</option>
                            </select>
                        </div>

                        {{-- Type de véhicule (optionnel) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-white mb-2">Type de véhicule <span class="text-gray-500 font-normal">(optionnel)</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative">
                                    <input type="radio" name="category" value="" class="peer sr-only" checked>
                                    <div class="px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-xl text-center cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-500/10 hover:border-gray-600">
                                        <span class="text-sm font-medium text-white">Tous</span>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="category" value="citadine" class="peer sr-only">
                                    <div class="px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-xl text-center cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-500/10 hover:border-gray-600">
                                        <span class="text-sm font-medium text-white">Citadine</span>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="category" value="berline" class="peer sr-only">
                                    <div class="px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-xl text-center cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-500/10 hover:border-gray-600">
                                        <span class="text-sm font-medium text-white">Berline</span>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="category" value="suv" class="peer sr-only">
                                    <div class="px-4 py-3 bg-[#1a1a1a] border border-gray-700 rounded-xl text-center cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-500/10 hover:border-gray-600">
                                        <span class="text-sm font-medium text-white">SUV</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden field for airport filter --}}
                    <input type="hidden" name="airport" value="1">

                    {{-- Submit Button --}}
                    <div class="mt-8">
                        <button type="submit"
                                class="w-full md:w-auto md:min-w-[300px] md:mx-auto md:flex px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-500/20 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Voir les véhicules disponibles
                        </button>
                    </div>
                </form>

                {{-- Trust indicators --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-6 lg:gap-10 text-gray-400 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Livraison à l'aéroport</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Loueurs vérifiés</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Annulation gratuite</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
