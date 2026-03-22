<x-filament-widgets::widget>
    {{-- Welcome Banner with time-based greeting --}}
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon apres-midi' : 'Bonsoir');
        $userName = auth()->user()->name ?? 'Administrateur';
    @endphp

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 p-6 mb-6 shadow-xl">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="text-white">
                    <p class="text-white/70 text-sm font-medium">{{ $greeting }} !</p>
                    <h1 class="text-2xl font-bold">{{ $userName }}</h1>
                    <p class="text-white/80 text-sm mt-1">
                        Bienvenue dans votre centre de controle ResaDZ
                    </p>
                </div>
            </div>

            {{-- Quick stats in banner --}}
            <div class="flex items-center gap-4">
                <div class="px-5 py-3 bg-white/20 backdrop-blur-sm rounded-xl text-center min-w-[100px]">
                    <p class="text-xs text-white/70 font-medium">Vehicules actifs</p>
                    <p class="text-2xl font-bold text-white">{{ \App\Models\Vehicle::where('is_active', true)->count() }}</p>
                </div>
                <div class="px-5 py-3 bg-white/20 backdrop-blur-sm rounded-xl text-center min-w-[100px]">
                    <p class="text-xs text-white/70 font-medium">Loueurs</p>
                    <p class="text-2xl font-bold text-white">{{ \App\Models\Loueur::where('is_active', true)->count() }}</p>
                </div>
                <div class="px-5 py-3 bg-white/20 backdrop-blur-sm rounded-xl text-center min-w-[100px]">
                    <p class="text-xs text-white/70 font-medium">Reservations</p>
                    <p class="text-2xl font-bold text-white">{{ \App\Models\Booking::whereIn('status', ['pending', 'confirmed'])->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section explanation --}}
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-800 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-light-bulb class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h3 class="font-semibold text-blue-900 dark:text-blue-100">Acces rapide a vos outils</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    Ci-dessous, retrouvez toutes les sections de votre plateforme organisees par categorie.
                    Cliquez sur n'importe quel element pour y acceder directement.
                </p>
            </div>
        </div>
    </div>

    {{-- Quick Links Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">

        {{-- Gestion --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/25">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Gestion</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Loueurs & reservations</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="/admin/loueurs" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-building-storefront class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Loueurs</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Loueur::count() }} inscrits</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-amber-500 transition-colors" />
                </a>
                <a href="/admin/bookings" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Reservations</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Booking::where('status', 'pending')->count() }} en attente</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-amber-500 transition-colors" />
                </a>
                <a href="/admin/revenues" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-banknotes class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Revenus</span>
                        <p class="text-xs text-gray-400">Suivi financier</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-amber-500 transition-colors" />
                </a>
            </div>
        </div>

        {{-- Catalogue --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <x-heroicon-o-squares-2x2 class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Catalogue</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Vehicules & categories</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="/admin/vehicles" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-truck class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Vehicules</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Vehicle::count() }} au total</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition-colors" />
                </a>
                <a href="/admin/brands" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-bookmark class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Marques</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Brand::count() }} marques</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition-colors" />
                </a>
                <a href="/admin/categories" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-tag class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Categories</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Category::count() }} types</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition-colors" />
                </a>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                    <x-heroicon-o-document-text class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Contenu</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Blog & visuels</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="/admin/blog-posts" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-newspaper class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Articles</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\BlogPost::count() }} publies</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 transition-colors" />
                </a>
                <a href="/admin/hero-slides" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-photo class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Slides accueil</span>
                        <p class="text-xs text-gray-400">Carrousel principal</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 transition-colors" />
                </a>
                <a href="/admin/popups" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-window class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Popups</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Popup::where('is_active', true)->count() }} actifs</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 transition-colors" />
                </a>
            </div>
        </div>

        {{-- Marketing --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center shadow-lg shadow-purple-500/25">
                    <x-heroicon-o-megaphone class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Marketing</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Leads & analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="/admin/statistics" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-chart-bar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Statistiques</span>
                        <p class="text-xs text-gray-400">Analyses detaillees</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-purple-500 transition-colors" />
                </a>
                <a href="/admin/leads" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-user-plus class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Leads</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\Lead::count() }} contacts</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-purple-500 transition-colors" />
                </a>
                <a href="/admin/newsletter-subscribers" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-envelope class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Newsletter</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\NewsletterSubscriber::count() }} abonnes</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-purple-500 transition-colors" />
                </a>
            </div>
        </div>

        {{-- Configuration --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-500 to-slate-600 flex items-center justify-center shadow-lg shadow-gray-500/25">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Configuration</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Parametres systeme</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="/admin/platform-settings" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Parametres</span>
                        <p class="text-xs text-gray-400">Config generale</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                </a>
                <a href="/admin/boost-packages" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-rocket-launch class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Packs Boost</span>
                        <p class="text-xs text-gray-400">{{ \App\Models\BoostPackage::count() }} formules</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                </a>
                <a href="/admin/home-selection" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <x-heroicon-o-star class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Selection accueil</span>
                        <p class="text-xs text-gray-400">Mise en avant</p>
                    </div>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" />
                </a>
            </div>
        </div>
    </div>

    {{-- Help tip at bottom --}}
    <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-800 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-question-mark-circle class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            </div>
            <p class="text-sm text-amber-700 dark:text-amber-300">
                <span class="font-semibold">Besoin d'aide ?</span>
                Utilisez le menu lateral pour naviguer entre les sections, ou cliquez directement sur les cartes ci-dessus pour un acces rapide.
            </p>
        </div>
    </div>
</x-filament-widgets::widget>
