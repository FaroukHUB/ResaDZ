<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Loueur;
use App\Models\Vehicle;

class HomeController extends Controller
{
    public function index()
    {
        $featuredVehicles = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('is_active', true)
            ->where('status', 'available')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
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

        return view('front.pages.home', compact(
            'featuredVehicles',
            'brands',
            'categories',
            'loueurs',
            'totalVehicles',
            'totalLoueurs'
        ));
    }
}
