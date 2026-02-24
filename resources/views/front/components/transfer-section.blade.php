{{-- Transfer/Taxi Section --}}
<section class="relative py-24 overflow-hidden">
    {{-- Background Image with Overlay --}}
    <div class="absolute inset-0">
        <img src="{{ asset('assets/images/transfer-bg.jpg') }}" alt="Service de transfert" class="w-full h-full object-cover"
             onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-gradient-to-r from-neutral-900/95 via-neutral-900/85 to-neutral-900/70"></div>
    </div>

    {{-- Decorative Elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-green-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-green-500/10 rounded-full blur-3xl"></div>
        {{-- Road line animation --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-green-500/50 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Text Content --}}
            <div class="text-white">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></div>
                    <span class="text-green-400 text-sm font-semibold uppercase tracking-wider">Transferts</span>
                </div>

                <h2 class="text-4xl lg:text-5xl font-black leading-tight mb-6">
                    Besoin d'un
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-green-500">transfert ?</span>
                </h2>

                <p class="text-lg text-white/70 leading-relaxed mb-8">
                    Aéroport, gare, hôtel... Nos partenaires vous conduisent partout en Algérie.
                    Service professionnel avec chauffeur, véhicule climatisé et bagages inclus.
                </p>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-white/80 text-sm">Chauffeur professionnel</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-white/80 text-sm">Véhicule climatisé</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-white/80 text-sm">Bagages inclus</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-white/80 text-sm">Prix fixe garanti</span>
                    </div>
                </div>
            </div>

            {{-- Right: Search Form --}}
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-3xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 p-8">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        Rechercher un transfert
                    </h3>

                    <form action="{{ route('transfers.search') }}" method="GET" class="space-y-4">
                        {{-- Départ --}}
                        <div>
                            <label for="transfer-departure" class="block text-sm font-medium text-white/70 mb-1">Lieu de départ</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="text" name="departure" id="transfer-departure" placeholder="Ex: Aéroport Houari Boumediene"
                                       class="w-full pl-11 pr-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>

                        {{-- Destination --}}
                        <div>
                            <label for="transfer-destination" class="block text-sm font-medium text-white/70 mb-1">Destination</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input type="text" name="destination" id="transfer-destination" placeholder="Ex: Alger centre, Hôtel..."
                                       class="w-full pl-11 pr-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>

                        {{-- Date & Heure --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="transfer-date" class="block text-sm font-medium text-white/70 mb-1">Date</label>
                                <input type="date" name="date" id="transfer-date" min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent [color-scheme:dark]">
                            </div>
                            <div>
                                <label for="transfer-time" class="block text-sm font-medium text-white/70 mb-1">Heure</label>
                                <select name="time" id="transfer-time" class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    @for($h = 0; $h <= 23; $h++)
                                        <option value="{{ sprintf('%02d:00', $h) }}" {{ $h == 10 ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Passagers --}}
                        <div>
                            <label for="transfer-passengers" class="block text-sm font-medium text-white/70 mb-1">Passagers</label>
                            <select name="passengers" id="transfer-passengers" class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-green-600 to-green-500 text-white font-bold rounded-xl hover:from-green-700 hover:to-green-600 transition-all shadow-lg shadow-green-500/25 flex items-center justify-center gap-2 text-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Trouver un transfert
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
