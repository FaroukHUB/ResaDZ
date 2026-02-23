<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Loueur;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('vehicles.is_active', true)
            ->where('vehicles.status', 'available');

        // Filtres
        if ($request->filled('brand')) {
            $query->where('vehicles.brand_id', $request->brand);
        }
        if ($request->filled('category')) {
            $query->where('vehicles.category_id', $request->category);
        }
        if ($request->filled('transmission')) {
            $query->where('vehicles.transmission', $request->transmission);
        }
        if ($request->filled('fuel')) {
            $query->where('vehicles.fuel_type', $request->fuel);
        }
        if ($request->filled('min_price')) {
            $query->where('vehicles.price_per_day', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('vehicles.price_per_day', '<=', $request->max_price);
        }
        if ($request->filled('wilaya')) {
            $query->whereHas('loueur', fn ($q) => $q->where('wilaya', $request->wilaya));
        }

        // Filtre aéroport : prioriser les loueurs avec livraison aéroport
        if ($request->filled('airport') && $request->airport == '1') {
            $query->whereHas('loueur.settings', function ($q) {
                $q->where('key', 'badge_airport')
                  ->where('value', 'true');
            });
        }

        // Filtre sélection (véhicules mis en avant)
        if ($request->filled('selection') && $request->selection == '1') {
            $query->where('vehicles.is_in_selection', true)
                  ->orderBy('vehicles.selection_order');
        }

        // Filtrage par disponibilité (dates de réservation)
        $pickupDate = null;
        $returnDate = null;

        if ($request->filled('pickup_date') && $request->filled('return_date')) {
            try {
                $pickupDate = Carbon::parse($request->pickup_date)->startOfDay();
                $returnDate = Carbon::parse($request->return_date)->endOfDay();

                // Exclure les véhicules qui ont des réservations confirmées/en attente pendant cette période
                $bookedVehicleIds = Booking::whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($q) use ($pickupDate, $returnDate) {
                        // Réservation qui chevauche la période demandée
                        $q->where(function ($sub) use ($pickupDate, $returnDate) {
                            $sub->where('start_date', '<=', $returnDate)
                                ->where('end_date', '>=', $pickupDate);
                        });
                    })
                    ->pluck('vehicle_id')
                    ->unique()
                    ->toArray();

                if (!empty($bookedVehicleIds)) {
                    $query->whereNotIn('vehicles.id', $bookedVehicleIds);
                }
            } catch (\Exception $e) {
                // Ignorer les dates invalides
            }
        }

        // Joindre les boosts actifs pour prioriser
        $query->leftJoin('vehicle_boosts', function ($join) {
            $join->on('vehicles.id', '=', 'vehicle_boosts.vehicle_id')
                ->where('vehicle_boosts.status', '=', 'active')
                ->where('vehicle_boosts.ends_at', '>=', now());
        })
        ->select('vehicles.*')
        ->selectRaw('CASE WHEN vehicle_boosts.id IS NOT NULL THEN 1 ELSE 0 END as has_boost');

        // Tri - les boostés toujours en premier
        $sort = $request->get('sort', 'recent');
        $query = match ($sort) {
            'price_asc' => $query->orderByDesc('has_boost')->orderBy('vehicles.price_per_day', 'asc'),
            'price_desc' => $query->orderByDesc('has_boost')->orderBy('vehicles.price_per_day', 'desc'),
            default => $query->orderByDesc('has_boost')->orderByDesc('vehicles.is_featured')->orderByDesc('vehicles.created_at'),
        };

        $vehicles = $query->paginate(12)->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        // Wilayas pour le filtre
        $wilayas = Loueur::where('is_active', true)
            ->whereNotNull('wilaya')
            ->distinct()
            ->pluck('wilaya')
            ->sort()
            ->values();

        return view('front.pages.vehicles', compact(
            'vehicles',
            'brands',
            'categories',
            'wilayas',
            'pickupDate',
            'returnDate'
        ));
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

    /**
     * SEO page: vehicles by wilaya.
     */
    public function byWilaya(string $wilaya)
    {
        // Normalize wilaya slug to name
        $wilayaName = str_replace('-', ' ', ucwords($wilaya, '-'));

        // Find loueurs in this wilaya
        $loueurIds = Loueur::where('is_active', true)
            ->where('is_suspended', false)
            ->where(function ($q) use ($wilayaName, $wilaya) {
                $q->whereRaw('LOWER(wilaya) = ?', [strtolower($wilayaName)])
                  ->orWhereRaw('LOWER(wilaya) = ?', [strtolower($wilaya)]);
            })
            ->pluck('id');

        $vehicles = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('is_active', true)
            ->where('status', 'available')
            ->whereIn('loueur_id', $loueurIds)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $loueurs = Loueur::where('is_active', true)
            ->whereIn('id', $loueurIds)
            ->withCount('vehicles')
            ->get();

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $totalVehicles = $vehicles->total();

        return view('front.pages.vehicles-by-wilaya', compact(
            'vehicles',
            'loueurs',
            'brands',
            'categories',
            'wilayaName',
            'totalVehicles'
        ));
    }

    /**
     * Compare vehicles side by side.
     */
    public function compare(Request $request)
    {
        $ids = $request->get('ids', '');
        $vehicleIds = array_filter(explode(',', $ids));

        if (count($vehicleIds) < 2 || count($vehicleIds) > 3) {
            return redirect()->route('vehicles.index')->with('error', 'Sélectionnez 2 ou 3 véhicules à comparer.');
        }

        $vehicles = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('is_active', true)
            ->whereIn('id', $vehicleIds)
            ->get();

        return view('front.pages.compare', compact('vehicles'));
    }
}
