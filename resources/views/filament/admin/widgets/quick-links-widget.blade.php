<x-filament-widgets::widget>
    {{-- Welcome Banner --}}
    <div class="welcome-banner mb-6">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Bienvenue, Administrateur</h1>
                    <p class="text-white/80 text-sm">Gerez votre plateforme ResaDZ</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white/20 rounded-lg text-center">
                    <p class="text-xs text-white/70">Vehicules</p>
                    <p class="text-xl font-bold">{{ \App\Models\Vehicle::where('is_active', true)->where('status', 'available')->count() }}</p>
                </div>
                <div class="px-4 py-2 bg-white/20 rounded-lg text-center">
                    <p class="text-xs text-white/70">Loueurs</p>
                    <p class="text-xl font-bold">{{ \App\Models\Loueur::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        {{-- Gestion --}}
        <div class="card-modern">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Gestion</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/loueurs" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/20 flex items-center justify-center">
                        <x-heroicon-o-building-storefront class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Loueurs</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Loueur::count() }}</p>
                    </div>
                </a>
                <a href="/admin/bookings" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/20 flex items-center justify-center">
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Reservations</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Booking::count() }}</p>
                    </div>
                </a>
                <a href="/admin/revenues" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/20 flex items-center justify-center">
                        <x-heroicon-o-currency-dollar class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Revenus</span>
                        <p class="text-xs text-gray-500">Finances</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Catalogue --}}
        <div class="card-modern">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Catalogue</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/vehicles" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-heroicon-o-truck class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Vehicules</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Vehicle::count() }}</p>
                    </div>
                </a>
                <a href="/admin/brands" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-heroicon-o-bookmark class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Marques</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Brand::count() }}</p>
                    </div>
                </a>
                <a href="/admin/categories" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center">
                        <x-heroicon-o-tag class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Categories</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Category::count() }}</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="card-modern">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Contenu</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/blog-posts" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <x-heroicon-o-newspaper class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Articles</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\BlogPost::count() }}</p>
                    </div>
                </a>
                <a href="/admin/hero-slides" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <x-heroicon-o-photo class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Slides</span>
                        <p class="text-xs text-gray-500">Accueil</p>
                    </div>
                </a>
                <a href="/admin/popups" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <x-heroicon-o-window class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Popups</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Popup::count() }}</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="card-modern">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Analytics</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/statistics" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/20 flex items-center justify-center">
                        <x-heroicon-o-chart-bar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Stats</span>
                        <p class="text-xs text-gray-500">Analyses</p>
                    </div>
                </a>
                <a href="/admin/leads" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/20 flex items-center justify-center">
                        <x-heroicon-o-user-plus class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Leads</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\Lead::count() }}</p>
                    </div>
                </a>
                <a href="/admin/newsletter-subscribers" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/20 flex items-center justify-center">
                        <x-heroicon-o-envelope class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Newsletter</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\NewsletterSubscriber::count() }}</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Configuration --}}
        <div class="card-modern">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gray-400 to-slate-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Config</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/platform-settings" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center">
                        <x-heroicon-o-cog-8-tooth class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Parametres</span>
                        <p class="text-xs text-gray-500">General</p>
                    </div>
                </a>
                <a href="/admin/lead-capture-settings" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center">
                        <x-heroicon-o-cursor-arrow-ripple class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Lead Capture</span>
                        <p class="text-xs text-gray-500">Popups</p>
                    </div>
                </a>
                <a href="/admin/boost-packages" class="nav-item-modern">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center">
                        <x-heroicon-o-rocket-launch class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Boost</span>
                        <p class="text-xs text-gray-500">{{ \App\Models\BoostPackage::count() }} packs</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
