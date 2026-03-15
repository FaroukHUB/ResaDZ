{{-- Section fusionnée : Voiture à l'arrivée OU Transfert --}}
<section class="relative py-16 lg:py-24 overflow-hidden">
    {{-- Hero Image --}}
    <div class="absolute inset-0">
        @if(file_exists(public_path('assets/airport-hero.jpg')))
            <img src="{{ asset('assets/airport-hero.jpg') }}" alt="Service aéroport" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full bg-gradient-to-br from-neutral-800 via-neutral-900 to-black"></div>
        @endif
        <div class="absolute inset-0 bg-black/70"></div>
    </div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Title --}}
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-white">
                Besoin d'un véhicule à votre arrivée ?
            </h2>
            <p class="mt-4 text-white/60 text-lg max-w-2xl mx-auto">
                Réservez une voiture en libre-service ou un transfert avec chauffeur
            </p>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'voiture' }" class="max-w-3xl mx-auto">
            {{-- Tab Buttons --}}
            <div class="flex bg-white/10 backdrop-blur rounded-xl p-1 mb-8">
                <button @click="tab = 'voiture'"
                        :class="tab === 'voiture' ? 'bg-green-600 text-white shadow-lg' : 'text-white/70 hover:text-white'"
                        class="flex-1 flex items-center justify-center gap-3 py-4 rounded-lg font-bold text-sm md:text-base transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5"/>
                    </svg>
                    Louer une voiture
                </button>
                <button @click="tab = 'transfert'"
                        :class="tab === 'transfert' ? 'bg-green-600 text-white shadow-lg' : 'text-white/70 hover:text-white'"
                        class="flex-1 flex items-center justify-center gap-3 py-4 rounded-lg font-bold text-sm md:text-base transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    Réserver un transfert
                </button>
            </div>

            {{-- Voiture Form --}}
            <div x-show="tab === 'voiture'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="{{ route('vehicles.index') }}" method="GET" class="bg-black/70 backdrop-blur-xl rounded-2xl p-6 lg:p-8 border border-white/10 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Date d'arrivée</label>
                            <input type="date" name="pickup_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent [color-scheme:dark]">
                        </div>
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Date de retour</label>
                            <input type="date" name="return_date" value="{{ date('Y-m-d', strtotime('+5 days')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent [color-scheme:dark]">
                        </div>
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Aéroport</label>
                            <select name="wilaya" class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none truncate">
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
                                <option value="Ghardaïa">Ghardaïa - Noumérat</option>
                                <option value="El Oued">El Oued - Guemar</option>
                                <option value="Tamanrasset">Tamanrasset - Aguenar</option>
                                <option value="Ouargla">Ouargla - Ain Beida</option>
                            </select>
                        </div>
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Type de véhicule</label>
                            <select name="category" class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none truncate">
                                <option value="">Tous les types</option>
                                <option value="citadine">Citadine</option>
                                <option value="berline">Berline</option>
                                <option value="suv">SUV</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="airport" value="1">
                    <div class="mt-6">
                        <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition shadow-lg shadow-green-600/30 flex items-center justify-center gap-2 text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Voir les véhicules disponibles
                        </button>
                    </div>
                </form>
            </div>

            {{-- Transfert Form --}}
            <div x-show="tab === 'transfert'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <form action="{{ route('transfers.search') }}" method="GET" class="bg-black/70 backdrop-blur-xl rounded-2xl p-6 lg:p-8 border border-white/10 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Lieu de départ</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="text" name="departure" placeholder="Ex: Aéroport Houari Boumediene"
                                       class="w-full pl-11 pr-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Destination</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="text" name="destination" placeholder="Ex: Alger centre, Hôtel..."
                                       class="w-full pl-11 pr-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Date</label>
                            <input type="date" name="date" min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent [color-scheme:dark]">
                        </div>
                        <div class="min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Heure</label>
                            <select name="time" class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none truncate">
                                @for($h = 0; $h <= 23; $h++)
                                    <option value="{{ sprintf('%02d:00', $h) }}" {{ $h == 10 ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="md:col-span-2 min-w-0">
                            <label class="block text-sm font-medium text-white/70 mb-2">Passagers</label>
                            <select name="passengers" class="w-full px-4 py-3.5 bg-neutral-900 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none truncate">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition shadow-lg shadow-green-600/30 flex items-center justify-center gap-2 text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Trouver un transfert
                        </button>
                    </div>
                </form>
            </div>

            {{-- Trust indicators --}}
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6 lg:gap-10 text-white/50 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Livraison à l'aéroport
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Chauffeur professionnel
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Prix fixe garanti
                </div>
            </div>
        </div>
    </div>
</section>
