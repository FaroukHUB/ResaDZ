@extends('front.layouts.app')

@section('title', 'Comment \u00e7a marche - ' . \App\Models\Setting::get('company_name', 'ResaDZ'))
@section('meta_description', 'D\u00e9couvrez comment fonctionne ResaDZ : service gratuit pour les locataires, visibilit\u00e9 et gestion simplifi\u00e9e pour les loueurs. Transparence totale sur notre mod\u00e8le.')

@section('content')

    <!-- Hero -->
    <section class="relative py-16 lg:py-24 bg-neutral-950 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-green-500/20 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-green-500/10 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-green-500/10 border border-green-500/20 rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-green-400 text-sm font-medium">Transparence totale</span>
            </div>
            <h1 class="text-3xl lg:text-5xl font-black text-white leading-tight">
                Comment <span class="text-green-400">fonctionne</span> ResaDZ ?
            </h1>
            <p class="mt-4 text-white/60 text-lg max-w-2xl mx-auto">
                Une plateforme simple, transparente et conçue pour faciliter la location de véhicules en Algérie. Découvrez comment ça marche pour vous.
            </p>
        </div>
    </section>

    <!-- Tabs -->
    <section class="py-12 lg:py-20 bg-gray-50" x-data="{ tab: 'locataire' }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Tab Switcher --}}
            <div class="flex justify-center mb-12 lg:mb-16">
                <div class="inline-flex bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200">
                    <button @click="tab = 'locataire'" :class="tab === 'locataire' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900'" class="px-6 lg:px-10 py-3 rounded-xl font-semibold text-sm lg:text-base transition-all">
                        Je cherche une voiture
                    </button>
                    <button @click="tab = 'loueur'" :class="tab === 'loueur' ? 'bg-neutral-900 text-white shadow-md' : 'text-gray-600 hover:text-gray-900'" class="px-6 lg:px-10 py-3 rounded-xl font-semibold text-sm lg:text-base transition-all">
                        Je suis loueur
                    </button>
                </div>
            </div>

            {{-- ===================== --}}
            {{-- TAB LOCATAIRE         --}}
            {{-- ===================== --}}
            <div x-show="tab === 'locataire'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Badge --}}
                <div class="text-center mb-10">
                    <div class="inline-flex items-center gap-2 bg-green-50 border border-green-200 rounded-full px-5 py-2">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span class="text-green-800 font-bold text-sm">Inscription gratuite &mdash; Aucun frais pour les locataires</span>
                    </div>
                </div>

                {{-- 3 steps --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mb-14">
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100 text-center">
                        <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <span class="text-green-700 font-black text-xl">1</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Recherchez</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Indiquez votre lieu, vos dates et le type de véhicule souhaité. Filtrez par prix, catégorie ou wilaya.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100 text-center">
                        <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <span class="text-green-700 font-black text-xl">2</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Comparez</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Consultez les offres de loueurs vérifiés avec les prix, conditions, photos et avis clients. Tout est transparent.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100 text-center">
                        <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <span class="text-green-700 font-black text-xl">3</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Réservez</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Réservez directement en ligne. Le loueur reçoit votre demande et vous confirme la disponibilité.</p>
                    </div>
                </div>

                {{-- Avantages locataire --}}
                <div class="bg-white rounded-2xl p-6 lg:p-10 border border-gray-100 mb-14">
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-8 text-center">Vos avantages en tant que locataire</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Inscription gratuite</p>
                                <p class="text-gray-500 text-xs mt-0.5">La recherche et la comparaison sont entièrement gratuites</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Aucun frais de service</p>
                                <p class="text-gray-500 text-xs mt-0.5">Vous payez uniquement le prix affiché par le loueur, sans frais supplémentaires</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Loueurs vérifiés</p>
                                <p class="text-gray-500 text-xs mt-0.5">Chaque partenaire est validé et noté par la communauté</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Comparaison facile</p>
                                <p class="text-gray-500 text-xs mt-0.5">Comparez les véhicules, prix et avis en un seul endroit</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Location & transfert</p>
                                <p class="text-gray-500 text-xs mt-0.5">Réservez un véhicule ou un chauffeur privé depuis la même plateforme</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Suivi de réservation</p>
                                <p class="text-gray-500 text-xs mt-0.5">Espace client dédié, messagerie directe avec le loueur</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA locataire --}}
                <div class="text-center">
                    <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition shadow-lg shadow-green-600/25">
                        Voir les véhicules disponibles
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>

            {{-- ===================== --}}
            {{-- TAB LOUEUR             --}}
            {{-- ===================== --}}
            <div x-show="tab === 'loueur'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Intro --}}
                <div class="text-center mb-10">
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">Vous êtes loueur de voitures ou chauffeur ? ResaDZ vous apporte de la visibilité et des outils de gestion professionnels.</p>
                </div>

                {{-- Comment ça marche loueur --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 mb-14">
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <span class="text-gray-800 font-black text-xl">1</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Inscrivez-vous</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Créez votre compte loueur gratuitement. Ajoutez vos informations, documents et coordonnées professionnelles.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <span class="text-gray-800 font-black text-xl">2</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Publiez vos véhicules</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Ajoutez vos véhicules avec photos, tarifs et disponibilités. Vous gérez tout depuis votre tableau de bord.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 lg:p-8 border border-gray-100 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <span class="text-gray-800 font-black text-xl">3</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Recevez des réservations</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Les clients vous trouvent, comparent et réservent directement. Vous recevez les demandes et confirmez.</p>
                    </div>
                </div>

                {{-- Modèle économique --}}
                <div class="bg-neutral-950 rounded-2xl p-6 lg:p-10 mb-14">
                    <div class="text-center mb-8">
                        <h3 class="text-xl lg:text-2xl font-bold text-white mb-2">Notre modèle, en toute transparence</h3>
                        <p class="text-white/50 text-sm">Un système simple et équitable pour tous</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-center">
                            <div class="text-3xl font-black text-green-400 mb-2">0 DA</div>
                            <p class="text-white font-semibold text-sm mb-1">Inscription</p>
                            <p class="text-white/40 text-xs">Créez votre compte et publiez vos véhicules sans aucun frais initial</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-center">
                            <div class="text-3xl font-black text-green-400 mb-2">0 DA</div>
                            <p class="text-white font-semibold text-sm mb-1">Abonnement</p>
                            <p class="text-white/40 text-xs">Aucun abonnement mensuel ni engagement. Vous ne payez que quand vous louez</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-center">
                            <div class="text-3xl font-black text-amber-400 mb-2">Frais / jour</div>
                            <p class="text-white font-semibold text-sm mb-1">Par jour de location confirmée</p>
                            <p class="text-white/40 text-xs">Un petit montant fixe par jour de location, prélevé uniquement sur les réservations confirmées</p>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-white/40 text-xs">Le premier mois est offert. Le montant exact des frais journaliers est communiqué lors de l'inscription.</p>
                    </div>
                </div>

                {{-- Avantages loueur --}}
                <div class="bg-white rounded-2xl p-6 lg:p-10 border border-gray-100 mb-14">
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-8 text-center">Ce que ResaDZ vous apporte</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Visibilité en ligne</p>
                                <p class="text-gray-500 text-xs mt-0.5">Vos véhicules apparaissent sur la plateforme, référencée sur Google</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Tableau de bord complet</p>
                                <p class="text-gray-500 text-xs mt-0.5">Gérez véhicules, réservations, calendrier et finances au même endroit</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Messagerie intégrée</p>
                                <p class="text-gray-500 text-xs mt-0.5">Communiquez directement avec vos clients depuis la plateforme</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Avis clients</p>
                                <p class="text-gray-500 text-xs mt-0.5">Construisez votre réputation grâce aux avis vérifiés</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Contrats automatiques</p>
                                <p class="text-gray-500 text-xs mt-0.5">Générez des contrats de location PDF professionnels en un clic</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Boostez vos annonces</p>
                                <p class="text-gray-500 text-xs mt-0.5">Mettez en avant vos véhicules pour plus de visibilité</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA loueur --}}
                <div class="text-center">
                    <a href="{{ route('register') }}?type=loueur" class="inline-flex items-center gap-2 px-8 py-4 bg-neutral-900 text-white font-bold rounded-full hover:bg-neutral-800 transition shadow-lg">
                        Créer mon compte loueur
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <p class="mt-3 text-gray-400 text-sm">Inscription gratuite, aucun engagement</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ section --}}
    <section class="py-12 lg:py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl lg:text-3xl font-black text-gray-900">Questions fréquentes</h2>
            </div>

            <div x-data="{ active: null }" class="space-y-3">
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900 text-sm">Combien ça coûte pour un locataire ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-4" :class="active === 1 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 1" x-collapse class="px-5 pb-4">
                        <p class="text-sm text-gray-600">C'est entièrement gratuit ! L'inscription, la recherche, la comparaison et la réservation sont gratuites. Vous payez uniquement le prix affiché par le loueur, sans aucun frais de service supplémentaire.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900 text-sm">Comment ResaDZ se rémunère-t-il ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-4" :class="active === 2 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 2" x-collapse class="px-5 pb-4">
                        <p class="text-sm text-gray-600">ResaDZ prélève une commission uniquement auprès des loueurs, en pourcentage du montant de la location. Le taux est dégressif : 8% pour 1-5 jours, 6% pour 5-10 jours, et 5% pour plus de 10 jours. Les locataires ne paient aucune commission. L'inscription, la publication d'annonces et l'utilisation du tableau de bord sont gratuites.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900 text-sm">Y a-t-il des frais cachés pour le loueur ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-4" :class="active === 3 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 3" x-collapse class="px-5 pb-4">
                        <p class="text-sm text-gray-600">Non. Pas d'abonnement, pas de frais d'inscription, pas de frais de publication. Le seul coût est une commission en pourcentage sur les réservations confirmées (8%, 6% ou 5% selon la durée de location). Cette commission est visible avant confirmation dans votre espace partenaire.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 4 ? null : 4" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900 text-sm">Puis-je m'inscrire en tant que chauffeur ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-4" :class="active === 4 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 4" x-collapse class="px-5 pb-4">
                        <p class="text-sm text-gray-600">Oui. Lors de l'inscription, vous pouvez choisir le profil "Chauffeur" pour proposer des services de transfert aéroport, mise à disposition et transport privé.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 5 ? null : 5" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900 text-sm">Les prix affichés incluent-ils la commission ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-4" :class="active === 5 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 5" x-collapse class="px-5 pb-4">
                        <p class="text-sm text-gray-600">Le locataire voit exactement le prix affiché par le loueur, sans frais supplémentaires. La commission ResaDZ est prélevée uniquement au loueur et n'affecte pas le prix payé par le locataire.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
