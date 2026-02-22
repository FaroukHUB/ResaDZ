<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Loueur;
use App\Models\Setting;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        // Notre sélection pour vous (choisis manuellement depuis l'admin)
        $selectedVehicles = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('is_active', true)
            ->where('status', 'available')
            ->where('is_in_selection', true)
            ->orderBy('selection_order')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Véhicules par catégorie (seulement les "featured" choisis par l'admin)
        // Triés par prix croissant, limité à 4 pour l'affichage homepage
        $categories = Category::active()->ordered()->get();

        $vehiclesByCategory = [];
        foreach ($categories as $category) {
            $vehiclesByCategory[$category->slug] = Vehicle::with(['brand', 'category', 'loueur.settings'])
                ->where('is_active', true)
                ->where('status', 'available')
                ->where('category_id', $category->id)
                ->where('is_featured', true)
                ->orderBy('price_per_day', 'asc')
                ->limit(4)
                ->get();
        }

        $brands = Brand::orderBy('name')->get();

        $loueurs = Loueur::where('is_active', true)
            ->withCount('vehicles')
            ->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        $totalVehicles = Vehicle::where('is_active', true)->count();
        $totalLoueurs = Loueur::where('is_active', true)->count();

        // Get unique wilayas from active loueurs
        $wilayas = Loueur::where('is_active', true)
            ->whereNotNull('wilaya')
            ->distinct()
            ->pluck('wilaya')
            ->sort()
            ->values();

        // Get active hero slides
        $heroSlides = HeroSlide::active()->ordered()->get();

        // Get homepage content from settings
        $homeContent = [
            'hero_title' => Setting::get('hero_title', 'Louez votre voiture'),
            'hero_subtitle' => Setting::get('hero_subtitle', 'partout en Algérie'),
            'hero_description' => Setting::get('hero_description', 'Comparez les offres de loueurs vérifiés et réservez en quelques clics. Le meilleur de la location auto en DZ.'),
            'how_it_works_title' => Setting::get('how_it_works_title', 'Comment ça marche'),
            'how_it_works_subtitle' => Setting::get('how_it_works_subtitle', 'En 3 étapes simples'),
            'selection_title' => Setting::get('selection_title', 'Notre sélection pour vous'),
            'selection_subtitle' => Setting::get('selection_subtitle', 'Les véhicules que nous recommandons'),
            'vehicles_title' => Setting::get('vehicles_title', 'Véhicules disponibles'),
            'vehicles_subtitle' => Setting::get('vehicles_subtitle', 'Les meilleures offres du moment'),
            'loueurs_title' => Setting::get('loueurs_title', 'Nos loueurs partenaires'),
            'loueurs_subtitle' => Setting::get('loueurs_subtitle', 'Des professionnels vérifiés à votre service'),
            'cta_title' => Setting::get('cta_title', 'Vous êtes loueur de voitures ?'),
            'cta_description' => Setting::get('cta_description', 'Rejoignez ResaDZ et développez votre activité en ligne. Gérez vos véhicules, réservations et finances depuis un seul tableau de bord.'),
            'cta_button' => Setting::get('cta_button', 'Devenir partenaire'),
        ];

        return view('front.pages.home', compact(
            'selectedVehicles',
            'vehiclesByCategory',
            'brands',
            'categories',
            'loueurs',
            'totalVehicles',
            'totalLoueurs',
            'wilayas',
            'heroSlides',
            'homeContent'
        ));
    }
}
