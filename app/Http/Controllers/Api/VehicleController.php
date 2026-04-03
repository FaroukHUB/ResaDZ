<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    /**
     * Liste des véhicules avec filtres
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vehicle::with(['brand', 'category'])
            ->active()
            ->where('status', 'available');

        // Filtre par catégorie
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filtre par marque
        if ($request->has('brand')) {
            $query->byBrand($request->brand);
        }

        // Filtre par prix min/max
        if ($request->has('price_min')) {
            $query->where('price_per_day', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('price_per_day', '<=', $request->price_max);
        }

        // Véhicules mis en avant
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Recherche par nom
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'sort_order');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        // Pagination optionnelle
        if ($request->has('limit')) {
            $vehicles = $query->limit($request->limit)->get();
        } else {
            $vehicles = $query->get();
        }

        return response()->json([
            'success' => true,
            'data' => $vehicles->map(fn ($v) => $this->formatVehicle($v)),
            'count' => $vehicles->count(),
        ]);
    }

    /**
     * Véhicules mis en avant
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        $vehicles = Vehicle::with(['brand', 'category'])
            ->active()
            ->where('status', 'available')
            ->featured()
            ->ordered()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vehicles->map(fn ($v) => $this->formatVehicle($v)),
        ]);
    }

    /**
     * Détail d'un véhicule
     */
    public function show(string $slug): JsonResponse
    {
        $vehicle = Vehicle::with(['brand', 'category'])
            ->where('slug', $slug)
            ->active()
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatVehicle($vehicle, true),
        ]);
    }

    /**
     * Véhicules par catégorie
     */
    public function byCategory(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->active()->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie non trouvée',
            ], 404);
        }

        $vehicles = Vehicle::with(['brand', 'category'])
            ->where('category_id', $category->id)
            ->active()
            ->where('status', 'available')
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
            'data' => $vehicles->map(fn ($v) => $this->formatVehicle($v)),
            'count' => $vehicles->count(),
        ]);
    }

    /**
     * Véhicules par marque
     */
    public function byBrand(string $slug): JsonResponse
    {
        $brand = Brand::where('slug', $slug)->active()->first();

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Marque non trouvée',
            ], 404);
        }

        $vehicles = Vehicle::with(['brand', 'category'])
            ->where('brand_id', $brand->id)
            ->active()
            ->where('status', 'available')
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'brand' => [
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo ? asset($brand->logo) : null,
            ],
            'data' => $vehicles->map(fn ($v) => $this->formatVehicle($v)),
            'count' => $vehicles->count(),
        ]);
    }

    /**
     * Vérifier disponibilité
     */
    public function checkAvailability(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::where('slug', $slug)->active()->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé',
            ], 404);
        }

        $isAvailable = $vehicle->isAvailableForDates(
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'success' => true,
            'available' => $isAvailable,
            'vehicle' => $this->formatVehicle($vehicle),
        ]);
    }

    /**
     * Retourne la liste des dates indisponibles pour un véhicule (blocked, maintenance, reserved)
     */
    public function unavailableDates(string $slug): JsonResponse
    {
        $vehicle = Vehicle::where('slug', $slug)->active()->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'Véhicule non trouvé'], 404);
        }

        $availabilities = $vehicle->availabilities()
            ->where('end_date', '>=', now()->toDateString())
            ->get(['start_date', 'end_date', 'type']);

        // Expand date ranges into individual dates
        $dates = [];
        foreach ($availabilities as $a) {
            $current = $a->start_date->copy();
            $end = $a->end_date->copy();
            while ($current->lte($end)) {
                $dates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        // Add confirmed/active booking dates
        $bookings = $vehicle->bookings()
            ->whereIn('status', ['confirmed', 'active'])
            ->where('end_date', '>=', now()->toDateString())
            ->get(['start_date', 'end_date']);

        foreach ($bookings as $b) {
            $current = \Carbon\Carbon::parse($b->start_date);
            $end = \Carbon\Carbon::parse($b->end_date);
            while ($current->lte($end)) {
                $dates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        return response()->json([
            'success' => true,
            'dates' => array_values(array_unique($dates)),
        ]);
    }

    /**
     * Formater un véhicule pour l'API
     */
    private function formatVehicle(Vehicle $vehicle, bool $detailed = false): array
    {
        $data = [
            'id' => $vehicle->id,
            'slug' => $vehicle->slug,
            'model' => $vehicle->model,
            'full_name' => $vehicle->full_name,
            'brand' => [
                'name' => $vehicle->brand->name,
                'slug' => $vehicle->brand->slug,
                'logo' => $vehicle->brand->logo ? asset($vehicle->brand->logo) : null,
            ],
            'category' => [
                'name' => $vehicle->category->name,
                'slug' => $vehicle->category->slug,
            ],
            'price' => [
                'per_day' => $vehicle->price_per_day,
                'per_day_formatted' => number_format($vehicle->price_per_day, 0, ',', ' ') . ' DA',
                'per_day_eur' => $vehicle->price_per_day_eur,
                'per_week' => $vehicle->price_per_week,
                'per_month' => $vehicle->price_per_month,
            ],
            'image' => $vehicle->image ? asset($vehicle->image) : null,
            'is_featured' => $vehicle->is_featured,
            'status' => $vehicle->status,
        ];

        if ($detailed) {
            $data['details'] = [
                'transmission' => $vehicle->transmission,
                'fuel_type' => $vehicle->fuel_type,
                'seats' => $vehicle->seats,
                'doors' => $vehicle->doors,
                'luggage_capacity' => $vehicle->luggage_capacity,
                'year' => $vehicle->year,
            ];
            $data['gallery'] = $vehicle->gallery ?? [];
            $data['seo'] = [
                'title' => $vehicle->meta_title,
                'description' => $vehicle->meta_description,
            ];
        }

        return $data;
    }
}
