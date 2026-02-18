<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('is_active', true)
            ->where('status', 'available');

        // Filtres
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }
        if ($request->filled('fuel')) {
            $query->where('fuel_type', $request->fuel);
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }
        if ($request->filled('wilaya')) {
            $query->whereHas('loueur', fn ($q) => $q->where('wilaya', $request->wilaya));
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price_per_day', 'asc'),
            'price_desc' => $query->orderBy('price_per_day', 'desc'),
            default => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
        };

        $vehicles = $query->paginate(12)->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('front.pages.vehicles', compact('vehicles', 'brands', 'categories'));
    }

    public function show(string $slug)
    {
        $vehicle = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedVehicles = Vehicle::with(['brand', 'loueur.settings'])
            ->where('is_active', true)
            ->where('status', 'available')
            ->where('id', '!=', $vehicle->id)
            ->where(function ($q) use ($vehicle) {
                $q->where('category_id', $vehicle->category_id)
                  ->orWhere('loueur_id', $vehicle->loueur_id);
            })
            ->limit(4)
            ->get();

        return view('front.pages.vehicle-detail', compact('vehicle', 'relatedVehicles'));
    }
}
