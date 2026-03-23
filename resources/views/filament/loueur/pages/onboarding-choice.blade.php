<x-filament-panels::page>
    <div class="min-h-[80vh] flex items-center justify-center" style="background: #F8FAFF;">
        <div class="max-w-4xl w-full mx-auto px-4 py-12">

            {{-- Logo --}}
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-4" style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); box-shadow: 0 8px 32px rgba(255,107,44,0.25);">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 00-.879-2.121l-3.006-3.006A3 3 0 0014.12 8.25H9.75v9.375c0 .621.504 1.125 1.125 1.125h2.25" />
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                    Bienvenue sur ResaDZ ! 🎉
                </h1>
                <p class="text-gray-500 mt-3 text-lg max-w-lg mx-auto">
                    Comment allez-vous utiliser la plateforme ?
                </p>
            </div>

            {{-- Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slide-up" style="animation-delay: 0.15s;">

                {{-- Card Loueur --}}
                <div
                    class="onboarding-choice-card group relative bg-white rounded-2xl p-8 cursor-pointer transition-all duration-300 hover:-translate-y-1"
                    style="box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 2px solid transparent;"
                    wire:click="chooseLoueur"
                    onmouseenter="this.style.borderColor='#FF6B2C'; this.style.boxShadow='0 12px 40px rgba(255,107,44,0.15)';"
                    onmouseleave="this.style.borderColor='transparent'; this.style.boxShadow='0 4px 24px rgba(0,0,0,0.06)';"
                >
                    {{-- Icon --}}
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); box-shadow: 0 8px 24px rgba(255,107,44,0.3);">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 00-.879-2.121l-3.006-3.006A3 3 0 0014.12 8.25H9.75v9.375c0 .621.504 1.125 1.125 1.125h2.25" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">
                        Je loue des véhicules 🚗
                    </h3>
                    <p class="text-gray-500 text-center text-sm mb-6">
                        J'ai une ou plusieurs voitures que je souhaite mettre en location.
                        Je peux aussi proposer mes services de chauffeur.
                    </p>

                    {{-- Features --}}
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B);">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            Gérez votre flotte en ligne
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B);">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            Recevez des réservations 24h/24
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B);">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            Tableau de bord complet
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="text-center">
                        <span class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-semibold text-sm transition-all duration-200 group-hover:scale-105" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 4px 14px rgba(255,107,44,0.3);">
                            Commencer comme loueur
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </span>
                    </div>

                    {{-- Loading overlay --}}
                    <div wire:loading wire:target="chooseLoueur" class="absolute inset-0 bg-white/80 rounded-2xl flex items-center justify-center z-10">
                        <svg class="w-8 h-8 animate-spin" style="color: #FF6B2C;" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Card Chauffeur --}}
                <div
                    class="onboarding-choice-card group relative bg-white rounded-2xl p-8 cursor-pointer transition-all duration-300 hover:-translate-y-1"
                    style="box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 2px solid transparent;"
                    wire:click="chooseChauffeur"
                    onmouseenter="this.style.borderColor='#6366F1'; this.style.boxShadow='0 12px 40px rgba(99,102,241,0.15)';"
                    onmouseleave="this.style.borderColor='transparent'; this.style.boxShadow='0 4px 24px rgba(0,0,0,0.06)';"
                >
                    {{-- Icon --}}
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, #6366F1 0%, #818CF8 100%); box-shadow: 0 8px 24px rgba(99,102,241,0.3);">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" fill="none" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">
                        Je suis chauffeur 🧑‍✈️
                    </h3>
                    <p class="text-gray-500 text-center text-sm mb-6">
                        Je propose mes services de transport avec mon véhicule personnel.
                    </p>

                    {{-- Features --}}
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #818CF8);">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            Recevez des demandes de course
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #818CF8);">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            Gérez vos disponibilités
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-700">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #6366F1, #818CF8);">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            Suivez vos revenus
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="text-center">
                        <span class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-semibold text-sm transition-all duration-200 group-hover:scale-105" style="background: linear-gradient(135deg, #6366F1, #818CF8); box-shadow: 0 4px 14px rgba(99,102,241,0.3);">
                            Commencer comme chauffeur
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </span>
                    </div>

                    {{-- Loading overlay --}}
                    <div wire:loading wire:target="chooseChauffeur" class="absolute inset-0 bg-white/80 rounded-2xl flex items-center justify-center z-10">
                        <svg class="w-8 h-8 animate-spin" style="color: #6366F1;" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
        }
    </style>
</x-filament-panels::page>
