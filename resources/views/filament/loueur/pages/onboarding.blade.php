<x-filament-panels::page>
    <div class="max-w-5xl mx-auto">
        {{-- Welcome banner --}}
        <div class="mb-8 animate-slide-up">
            <div class="welcome-banner relative overflow-hidden rounded-2xl px-6 py-6 md:px-8 md:py-7" style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); box-shadow: 0 8px 32px rgba(255,107,44,0.25);">
                <div class="absolute inset-0 opacity-10">
                    <svg class="absolute -right-8 -top-8 w-56 h-56 text-white/20" fill="currentColor" viewBox="0 0 24 24"><path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/></svg>
                    <svg class="absolute -left-4 -bottom-4 w-32 h-32 text-white/10" fill="currentColor" viewBox="0 0 24 24"><path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div class="text-white">
                        <h1 class="text-2xl md:text-3xl font-bold flex items-center gap-3">
                            <x-heroicon-o-sparkles class="w-8 h-8" />
                            Bienvenue sur ResaDZ !
                        </h1>
                        <p class="text-white/90 mt-2 text-sm md:text-base max-w-xl">Configurez votre espace en quelques minutes pour commencer a recevoir des reservations.</p>
                    </div>
                    <div class="hidden md:flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-xl px-5 py-3 text-white" style="box-shadow: 0 2px 12px rgba(0,0,0,0.1);">
                        <x-heroicon-o-sparkles class="w-5 h-5" />
                        <span class="font-bold text-lg">{{ round(($currentStep / $totalSteps) * 100) }}%</span>
                        <span class="text-white/80 text-sm">complete</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress bar visual --}}
        <div class="mb-8 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6" style="box-shadow: 0 4px 24px rgba(255,107,44,0.08); border: 1px solid rgba(243,244,246,1);">
                <div class="flex justify-between items-center mb-4">
                    @foreach($this->getStepInfo() as $step => $info)
                        <div class="flex flex-col items-center flex-1 {{ $step < $totalSteps ? 'relative' : '' }}">
                            {{-- Step circle --}}
                            <div class="step-indicator w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500
                                @if($step < $currentStep)
                                    step-indicator-completed text-white
                                @elseif($step === $currentStep)
                                    step-indicator-active text-white scale-110
                                @else
                                    step-indicator-pending text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600
                                @endif"
                                @if($step < $currentStep)
                                    style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 3px 10px rgba(16,185,129,0.3);"
                                @elseif($step === $currentStep)
                                    style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 0 0 4px rgba(255,107,44,0.15), 0 4px 14px rgba(255,107,44,0.35);"
                                @endif
                            >
                                @if($step < $currentStep)
                                    <x-heroicon-s-check class="w-5 h-5" />
                                @else
                                    {{ $step }}
                                @endif
                            </div>
                            {{-- Step label --}}
                            <span class="mt-3 text-xs font-medium text-center hidden sm:block max-w-[80px] transition-colors duration-300
                                @if($step === $currentStep)
                                    font-bold
                                @elseif($step < $currentStep)
                                    text-green-600 dark:text-green-400
                                @else
                                    text-gray-400 dark:text-gray-500
                                @endif"
                                @if($step === $currentStep)
                                    style="color: #FF6B2C;"
                                @endif
                            >
                                {{ $info['title'] }}
                            </span>
                            {{-- Connector line --}}
                            @if($step < $totalSteps)
                                <div class="hidden sm:block absolute top-5 left-1/2 w-full h-1 rounded-full transition-all duration-500"
                                    style="transform: translateX(50%); {{ $step < $currentStep ? 'background: linear-gradient(90deg, #10B981, #059669);' : 'background: #e5e7eb;' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Progress percentage bar --}}
                <div class="progress-modern mt-4 h-2.5 rounded-full overflow-hidden" style="background: #f3f4f6;">
                    <div class="progress-modern-bar h-full rounded-full transition-all duration-700 ease-out" style="width: {{ ($currentStep / $totalSteps) * 100 }}%; background: linear-gradient(90deg, #FF6B2C, #F59E0B);"></div>
                </div>
            </div>
        </div>

        {{-- Current step info card --}}
        @php
            $stepInfo = $this->getStepInfo()[$currentStep] ?? [];
            $stepEmojis = [
                1 => '👋',
                2 => '📍',
                3 => '💳',
                4 => '🎁',
                5 => '📋',
                6 => '✨',
                7 => '🔔',
            ];
            $stepEncouragements = [
                1 => 'C\'est parti ! Parlez-nous de vous.',
                2 => 'Super ! Definissez vos zones de livraison.',
                3 => 'Genial ! Configurez vos paiements.',
                4 => 'Excellent ! Ajoutez vos options extras.',
                5 => 'Presque fini ! Vos conditions de location.',
                6 => 'Bravo ! Personnalisez vos badges.',
                7 => 'Derniere ligne droite ! Activez vos notifications.',
            ];
        @endphp
        <div class="mb-6 rounded-2xl overflow-hidden animate-slide-up" style="animation-delay: 0.15s; box-shadow: 0 4px 24px rgba(255,107,44,0.08); border-left: 4px solid #FF6B2C; background: linear-gradient(90deg, rgba(255,107,44,0.06) 0%, transparent 100%);">
            <div class="bg-white dark:bg-gray-800 p-6">
                <div class="flex items-start gap-4">
                    <div class="icon-container w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 4px 14px rgba(255,107,44,0.3);">
                        @if($stepInfo['icon'] ?? false)
                            <x-dynamic-component :component="$stepInfo['icon']" class="w-7 h-7 text-white" />
                        @else
                            <span class="text-xl font-bold text-white">{{ $currentStep }}</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="badge-modern inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold text-white" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 2px 8px rgba(255,107,44,0.25);">
                                Etape {{ $currentStep }}/{{ $totalSteps }}
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-2 flex items-center gap-2">
                            <span>{{ $stepEmojis[$currentStep] ?? '🚀' }}</span>
                            {{ $stepEncouragements[$currentStep] ?? ($stepInfo['title'] ?? '') }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ $stepInfo['description'] ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assistant tips per step --}}
        @php
            $tips = [
                1 => [
                    'title' => 'Conseil',
                    'text' => 'Un profil complet inspire confiance. Les clients preferent les agences avec une description claire et des coordonnees verifiables.',
                    'icon' => 'heroicon-o-light-bulb',
                    'color' => 'amber',
                ],
                2 => [
                    'title' => 'Important',
                    'text' => 'Sans zone de livraison, vos clients ne pourront pas reserver. Creez au moins une zone avec le lieu principal de prise en charge.',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'color' => 'red',
                ],
                3 => [
                    'title' => 'Astuce',
                    'text' => 'Un acompte de 20-30% est recommande pour securiser les reservations tout en restant attractif. Proposez plusieurs methodes de paiement.',
                    'icon' => 'heroicon-o-currency-dollar',
                    'color' => 'green',
                ],
                4 => [
                    'title' => 'Bon a savoir',
                    'text' => 'Les options populaires comme le siege bebe ou le GPS peuvent etre offertes gratuitement pour vous demarquer de la concurrence.',
                    'icon' => 'heroicon-o-gift',
                    'color' => 'purple',
                ],
                5 => [
                    'title' => 'Conseil juridique',
                    'text' => 'Des conditions claires protegent a la fois vous et vos clients. Precisez au minimum l\'age requis et les documents demandes.',
                    'icon' => 'heroicon-o-scale',
                    'color' => 'blue',
                ],
                6 => [
                    'title' => 'Marketing',
                    'text' => 'Les badges attirent l\'attention sur vos vehicules. N\'activez que ceux qui correspondent vraiment a vos services.',
                    'icon' => 'heroicon-o-sparkles',
                    'color' => 'pink',
                ],
                7 => [
                    'title' => 'Derniere etape !',
                    'text' => 'Activez au moins un canal de notification pour ne jamais manquer une reservation. Les notifications push sont les plus rapides.',
                    'icon' => 'heroicon-o-bell',
                    'color' => 'indigo',
                ],
            ];
            $tip = $tips[$currentStep] ?? null;
            $tipGradients = [
                'amber' => 'linear-gradient(135deg, #FFFBEB 0%, #FFF7ED 100%)',
                'red' => 'linear-gradient(135deg, #FEF2F2 0%, #FFF1F2 100%)',
                'green' => 'linear-gradient(135deg, #ECFDF5 0%, #F0FDF4 100%)',
                'purple' => 'linear-gradient(135deg, #FAF5FF 0%, #FDF4FF 100%)',
                'blue' => 'linear-gradient(135deg, #EFF6FF 0%, #EEF2FF 100%)',
                'pink' => 'linear-gradient(135deg, #FDF2F8 0%, #FFF1F2 100%)',
                'indigo' => 'linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%)',
            ];
            $tipDarkBgs = [
                'amber' => 'dark:from-amber-900/20 dark:to-orange-900/20',
                'red' => 'dark:from-red-900/20 dark:to-rose-900/20',
                'green' => 'dark:from-green-900/20 dark:to-emerald-900/20',
                'purple' => 'dark:from-purple-900/20 dark:to-violet-900/20',
                'blue' => 'dark:from-blue-900/20 dark:to-indigo-900/20',
                'pink' => 'dark:from-pink-900/20 dark:to-rose-900/20',
                'indigo' => 'dark:from-indigo-900/20 dark:to-violet-900/20',
            ];
            $tipBorders = [
                'amber' => '#F59E0B',
                'red' => '#EF4444',
                'green' => '#10B981',
                'purple' => '#8B5CF6',
                'blue' => '#3B82F6',
                'pink' => '#EC4899',
                'indigo' => '#6366F1',
            ];
            $iconGradients = [
                'amber' => 'linear-gradient(135deg, #F59E0B, #F97316)',
                'red' => 'linear-gradient(135deg, #EF4444, #F43F5E)',
                'green' => 'linear-gradient(135deg, #10B981, #059669)',
                'purple' => 'linear-gradient(135deg, #8B5CF6, #7C3AED)',
                'blue' => 'linear-gradient(135deg, #3B82F6, #6366F1)',
                'pink' => 'linear-gradient(135deg, #EC4899, #F43F5E)',
                'indigo' => 'linear-gradient(135deg, #6366F1, #7C3AED)',
            ];
        @endphp

        @if($tip)
            <div class="mb-6 rounded-2xl p-5 border animate-slide-up dark:bg-gray-800" style="animation-delay: 0.2s; background: {{ $tipGradients[$tip['color']] ?? $tipGradients['amber'] }}; border-color: {{ $tipBorders[$tip['color']] ?? $tipBorders['amber'] }}33; box-shadow: 0 4px 24px rgba(255,107,44,0.06);">
                <div class="flex items-start gap-4">
                    <div class="icon-container w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $iconGradients[$tip['color']] ?? $iconGradients['amber'] }}; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <x-dynamic-component :component="$tip['icon']" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>{{ $tip['title'] }}</span>
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">{{ $tip['text'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <div class="card-modern animate-slide-up" style="animation-delay: 0.25s; box-shadow: 0 4px 24px rgba(255,107,44,0.08);">
            <form wire:submit.prevent="{{ $currentStep === $totalSteps ? 'completeOnboarding' : 'nextStep' }}">
                {{ $this->form }}

                {{-- Navigation buttons --}}
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        @if($currentStep > 1)
                            <x-filament::button
                                type="button"
                                color="gray"
                                wire:click="previousStep"
                                class="group btn-modern"
                                style="border-radius: 0.75rem; transition: all 0.2s ease;"
                            >
                                <x-heroicon-s-arrow-left class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                                Precedent
                            </x-filament::button>
                        @else
                            <button
                                type="button"
                                wire:click="skipOnboarding"
                                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline decoration-dashed hover:decoration-solid transition-all"
                            >
                                Configurer plus tard
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-1.5">
                            @for($i = 1; $i <= $totalSteps; $i++)
                                <div class="h-2 rounded-full transition-all duration-300
                                    {{ $i < $currentStep ? 'w-2 bg-green-500' : '' }}
                                    {{ $i === $currentStep ? 'w-6' : '' }}
                                    {{ $i > $currentStep ? 'w-2 bg-gray-300 dark:bg-gray-600' : '' }}"
                                    @if($i === $currentStep) style="background: linear-gradient(90deg, #FF6B2C, #F59E0B);" @endif
                                ></div>
                            @endfor
                        </div>

                        @if($currentStep === $totalSteps)
                            <x-filament::button type="submit" color="success" class="group btn-modern rounded-xl" style="background: linear-gradient(135deg, #10B981, #059669); border: none; box-shadow: 0 4px 14px rgba(16,185,129,0.3); transition: all 0.2s ease; border-radius: 0.75rem; padding: 0.625rem 1.5rem;">
                                <x-heroicon-s-check class="w-4 h-4 mr-1 group-hover:scale-110 transition-transform" />
                                Terminer la configuration
                            </x-filament::button>
                        @else
                            <x-filament::button type="submit" class="group btn-gradient-primary rounded-xl" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); border: none; box-shadow: 0 4px 14px rgba(255,107,44,0.3); transition: all 0.2s ease; border-radius: 0.75rem; padding: 0.625rem 1.5rem;">
                                Continuer
                                <x-heroicon-s-arrow-right class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Skip link at bottom --}}
        @if($currentStep > 1)
            <div class="text-center mt-6 animate-slide-up" style="animation-delay: 0.3s;">
                <button
                    type="button"
                    wire:click="skipOnboarding"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all duration-200"
                    style="transition: all 0.2s ease;"
                >
                    <x-heroicon-o-forward class="w-4 h-4" />
                    Terminer plus tard et acceder au tableau de bord
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
