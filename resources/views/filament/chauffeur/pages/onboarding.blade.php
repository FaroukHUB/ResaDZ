<x-filament-panels::page>
    @php
        $stepInfo = $this->getStepInfo();
        $stepTitles = [
            0 => 'Avant de commencer 📋',
            1 => 'Parlez-nous de vous 👋',
            2 => 'Votre véhicule 🚗',
            3 => 'Quels services proposez-vous ? 🗺️',
            4 => 'Quand êtes-vous disponible ? 🕐',
            5 => 'Dernière étape ! Restez connecté 🔔',
        ];
        $stepIconColors = [
            0 => '#6366F1',
            1 => '#6366F1',
            2 => '#6366F1',
            3 => '#6366F1',
            4 => '#6366F1',
            5 => '#3B82F6',
        ];
        $stepIconGradients = [
            0 => 'linear-gradient(135deg, #6366F1, #818CF8)',
            1 => 'linear-gradient(135deg, #6366F1, #818CF8)',
            2 => 'linear-gradient(135deg, #6366F1, #818CF8)',
            3 => 'linear-gradient(135deg, #6366F1, #818CF8)',
            4 => 'linear-gradient(135deg, #6366F1, #818CF8)',
            5 => 'linear-gradient(135deg, #3B82F6, #6366F1)',
        ];
        $resabotMessages = [
            0 => '👋 Ces documents vous protègent et protègent vos clients. Prenez 2 minutes pour les lire !',
            1 => 'Une belle description rassure les clients. Expliquez votre expérience et votre style de conduite ! 😊',
            2 => 'Les clients choisissent souvent selon le type de véhicule. Précisez bien le nombre de places ! 🪑',
            3 => 'Les transferts aéroport sont très demandés — ajoutez vos trajets réguliers pour apparaître dans les recherches ! ✈️',
            4 => 'Les chauffeurs disponibles en soirée et le weekend reçoivent 40% de demandes en plus 🌙',
            5 => 'Activez WhatsApp — les clients précisent souvent les détails par message après la réservation 📱',
        ];
    @endphp

    <div class="max-w-5xl mx-auto">

        {{-- Progress bar --}}
        <div class="mb-8 onb-animate-in" style="animation-delay: 0.05s;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5" style="box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #f3f4f6;">
                <div class="flex justify-between items-start">
                    @foreach($stepInfo as $step => $info)
                        <div class="flex flex-col items-center flex-1 {{ $step < ($totalSteps - 1) ? 'relative' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500
                                @if($step < $currentStep) text-white
                                @elseif($step === $currentStep) text-white
                                @else text-gray-400 bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600
                                @endif"
                                @if($step < $currentStep)
                                    style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 3px 10px rgba(16,185,129,0.3);"
                                @elseif($step === $currentStep)
                                    style="background: {{ $stepIconGradients[$step] }}; box-shadow: 0 0 0 4px {{ $stepIconColors[$step] }}26, 0 4px 14px {{ $stepIconColors[$step] }}50; animation: onb-pulse-indigo 2s infinite;"
                                @endif
                            >
                                @if($step < $currentStep)
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                @else
                                    {{ $step }}
                                @endif
                            </div>
                            <span class="mt-2 text-[10px] font-medium text-center hidden sm:block max-w-[70px]
                                @if($step === $currentStep) font-bold
                                @elseif($step < $currentStep) text-green-600
                                @else text-gray-400
                                @endif"
                                @if($step === $currentStep) style="color: {{ $stepIconColors[$step] }};" @endif
                            >{{ $info['title'] }}</span>
                            @if($step < ($totalSteps - 1))
                                <div class="hidden sm:block absolute top-5 left-1/2 w-full h-0.5 rounded-full" style="transform: translateX(50%); {{ $step < $currentStep ? 'background: linear-gradient(90deg, #10B981, #059669);' : 'background: #e5e7eb;' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 h-2 rounded-full overflow-hidden" style="background: #f3f4f6;">
                    <div class="h-full rounded-full transition-all duration-700 ease-out" style="width: {{ (($currentStep + 1) / $totalSteps) * 100 }}%; background: {{ $stepIconGradients[$currentStep] }};"></div>
                </div>
            </div>
        </div>

        {{-- Step header --}}
        <div class="mb-6 rounded-2xl overflow-hidden onb-animate-in" style="animation-delay: 0.1s; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border-left: 4px solid {{ $stepIconColors[$currentStep] }}; background: linear-gradient(90deg, {{ $stepIconColors[$currentStep] }}0a 0%, transparent 100%);">
            <div class="bg-white dark:bg-gray-800 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 onb-bounce-in" style="background: {{ $stepIconGradients[$currentStep] }}; box-shadow: 0 4px 14px {{ $stepIconColors[$currentStep] }}40;">
                        @if(isset($stepInfo[$currentStep]['icon']))
                            <x-dynamic-component :component="$stepInfo[$currentStep]['icon']" class="w-7 h-7 text-white" />
                        @endif
                    </div>
                    <div class="flex-1">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white" style="background: {{ $stepIconGradients[$currentStep] }};">
                            Étape {{ $currentStep }}/{{ $totalSteps - 1 }}
                        </span>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ $stepTitles[$currentStep] ?? '' }}
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ $stepInfo[$currentStep]['description'] ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Résabot bubble --}}
        @if(isset($resabotMessages[$currentStep]))
            <div class="mb-6 flex items-start gap-3 onb-animate-in" style="animation-delay: 0.2s;">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 2px 8px rgba(255,107,44,0.3);">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-tl-none px-5 py-3.5 max-w-lg" style="border: 1px solid #FF6B2C20; box-shadow: 0 2px 12px rgba(255,107,44,0.06);">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $resabotMessages[$currentStep] }}</p>
                </div>
            </div>
        @endif

        {{-- STEP 0: CGU & CONTRAT CHAUFFEUR --}}
        @if($currentStep === 0)
            <div class="space-y-6 onb-animate-in" style="animation-delay: 0.25s;">

                {{-- CGU --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6" style="box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" style="color: #6366F1;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        Conditions Générales d'Utilisation
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">Veuillez lire et accepter nos CGU avant de continuer.</p>
                    <a href="/conditions-generales-utilisation" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-white mb-4" style="background: linear-gradient(135deg, #6366F1, #818CF8);">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        Lire les CGU
                    </a>
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model.live="cguAccepted" class="w-5 h-5 rounded border-gray-300 text-indigo-500 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">J'ai lu et j'accepte les CGU de ResaDZ</span>
                    </label>
                </div>

                {{-- Contrat Chauffeur --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6" style="box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" style="color: #6366F1;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        Contrat de Partenariat Chauffeur
                    </h3>
                    <div class="rounded-xl p-4 mb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed overflow-y-auto" style="max-height: 200px; background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <p class="font-bold mb-2">CONTRAT DE PARTENARIAT CHAUFFEUR — ResaDZ</p>
                        <p class="mb-2">Entre ResaDZ (la Plateforme) et le Chauffeur (vous).</p>

                        <p class="font-semibold mt-3 mb-1">1. OBJET</p>
                        <p class="mb-2">ResaDZ met à disposition une plateforme de mise en relation entre chauffeurs et clients en Algérie pour des services de transport.</p>

                        <p class="font-semibold mt-3 mb-1">2. COMMISSIONS ET FACTURATION</p>
                        <p class="mb-2">Le Chauffeur accepte une commission de 10% sur chaque course ou transfert confirmé.</p>
                        <p class="mb-2">Les paiements des clients sont effectués directement au Chauffeur. ResaDZ émet une facture hebdomadaire récapitulant les commissions dues. Le Chauffeur s'engage à régler ses factures dans les délais indiqués.</p>

                        <p class="font-semibold mt-3 mb-1">3. OBLIGATIONS DU CHAUFFEUR</p>
                        <ul class="list-disc list-inside mb-2 space-y-0.5">
                            <li>Posséder un permis de conduire valide</li>
                            <li>Avoir un véhicule en bon état et assuré</li>
                            <li>Respecter les horaires et trajets confirmés</li>
                            <li>Maintenir un comportement professionnel</li>
                            <li>Répondre aux demandes dans les 2 heures</li>
                            <li>Ne pas annuler moins de 4h avant la course</li>
                            <li>Ne pas contacter les clients hors plateforme pour leur première réservation</li>
                            <li>Maintenir une note minimale de 3.5/5</li>
                        </ul>

                        <p class="font-semibold mt-3 mb-1">4. OBLIGATIONS DE RESADZ</p>
                        <ul class="list-disc list-inside mb-2 space-y-0.5">
                            <li>Mettre en relation chauffeurs et clients</li>
                            <li>Sécuriser les paiements</li>
                            <li>Fournir un support en cas de litige</li>
                            <li>Émettre les factures hebdomadaires</li>
                        </ul>

                        <p class="font-semibold mt-3 mb-1">5. RÉSILIATION</p>
                        <p>Chaque partie peut mettre fin au contrat à tout moment, sous réserve que le Chauffeur soit à jour dans le règlement de ses factures ResaDZ.</p>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model.live="contratAccepted" class="w-5 h-5 rounded border-gray-300 text-indigo-500 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">J'ai lu et j'accepte le Contrat de Partenariat Chauffeur ResaDZ</span>
                    </label>
                </div>

                {{-- Button --}}
                <div class="flex justify-end">
                    <button
                        type="button"
                        wire:click="acceptCguAndContinue"
                        @if(!$cguAccepted || !$contratAccepted) disabled @endif
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm text-white transition-all duration-200"
                        style="{{ ($cguAccepted && $contratAccepted)
                            ? 'background: linear-gradient(135deg, #6366F1, #818CF8); box-shadow: 0 4px 14px rgba(99,102,241,0.3); cursor: pointer;'
                            : 'background: #CBD5E1; cursor: not-allowed;' }}"
                    >
                        Commencer la configuration
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </button>
                </div>
            </div>

        {{-- STEPS 1-5: FORM --}}
        @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 onb-animate-in" style="animation-delay: 0.25s; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                <form wire:submit.prevent="{{ $currentStep === ($totalSteps - 1) ? 'completeOnboarding' : 'nextStep' }}">
                    {{ $this->form }}

                    {{-- Navigation --}}
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            @if($currentStep > 1)
                                <button type="button" wire:click="previousStep"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-300 hover:bg-gray-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                                    Précédent
                                </button>
                            @else
                                <button type="button" wire:click="skipOnboarding"
                                    class="text-sm text-gray-400 hover:text-gray-600 underline decoration-dashed transition-all">
                                    Configurer plus tard
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="hidden sm:flex items-center gap-1.5">
                                @for($i = 0; $i < $totalSteps; $i++)
                                    <div class="rounded-full transition-all duration-300
                                        {{ $i < $currentStep ? 'w-2 h-2 bg-green-500' : '' }}
                                        {{ $i === $currentStep ? 'w-6 h-2' : '' }}
                                        {{ $i > $currentStep ? 'w-2 h-2 bg-gray-300' : '' }}"
                                        @if($i === $currentStep) style="background: {{ $stepIconGradients[$currentStep] }};" @endif
                                    ></div>
                                @endfor
                            </div>

                            @if($currentStep === ($totalSteps - 1))
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition-all"
                                    style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 4px 14px rgba(16,185,129,0.3);">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.841m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
                                    Commencer à recevoir des courses !
                                </button>
                            @else
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition-all"
                                    style="background: linear-gradient(135deg, #6366F1, #818CF8); box-shadow: 0 4px 14px rgba(99,102,241,0.3);">
                                    Continuer
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if($currentStep > 1)
                <div class="text-center mt-6 onb-animate-in" style="animation-delay: 0.3s;">
                    <button type="button" wire:click="skipOnboarding"
                        class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8.689c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062A1.125 1.125 0 013 16.811V8.69zM12.75 8.689c0-.864.933-1.405 1.683-.977l7.108 4.062a1.125 1.125 0 010 1.953l-7.108 4.062a1.125 1.125 0 01-1.683-.977V8.69z" /></svg>
                        Terminer plus tard
                    </button>
                </div>
            @endif
        @endif
    </div>

    <style>
        @keyframes onb-slide-in {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes onb-bounce-in {
            0% { opacity: 0; transform: scale(0.5); }
            60% { transform: scale(1.1); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes onb-pulse-indigo {
            0%, 100% { box-shadow: 0 0 0 4px rgba(99,102,241,0.15), 0 4px 14px rgba(99,102,241,0.35); }
            50% { box-shadow: 0 0 0 8px rgba(99,102,241,0.08), 0 4px 14px rgba(99,102,241,0.2); }
        }
        .onb-animate-in {
            animation: onb-slide-in 0.4s ease-out both;
        }
        .onb-bounce-in {
            animation: onb-bounce-in 0.5s ease-out both;
            animation-delay: 0.15s;
        }
    </style>
</x-filament-panels::page>
