<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Loueur;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        $featuredVehicles = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('is_active', true)
            ->where('status', 'available')
            ->leftJoin('vehicle_boosts', function ($join) {
                $join->on('vehicles.id', '=', 'vehicle_boosts.vehicle_id')
                    ->where('vehicle_boosts.status', '=', 'active')
                    ->where('vehicle_boosts.ends_at', '>=', now());
            })
            ->select('vehicles.*')
            ->selectRaw('CASE WHEN vehicle_boosts.id IS NOT NULL THEN 1 ELSE 0 END as has_boost')
            ->orderByDesc('has_boost')
            ->orderByDesc('is_featured')
            ->orderByDesc('vehicles.created_at')
            ->limit(8)
            ->get();

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

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

        return view('front.pages.home', compact(
            'featuredVehicles',
            'brands',
            'categories',
            'loueurs',
            'totalVehicles',
            'totalLoueurs',
            'wilayas',
            'heroSlides'
        ));
    }
}
