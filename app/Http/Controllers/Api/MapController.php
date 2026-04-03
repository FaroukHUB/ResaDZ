<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function vehiclesByWilaya(): JsonResponse
    {
        $data = Cache::remember('map_vehicles_by_wilaya', 600, function () {
            $wilayas = config('resadz.wilayas', []);
            $coordinates = config('resadz.wilayas_coordinates', []);

            // Count active vehicles per wilaya via loueur pivot
            $vehicleCounts = DB::table('vehicles')
                ->join('loueurs', 'vehicles.loueur_id', '=', 'loueurs.id')
                ->join('loueur_wilaya', 'loueurs.id', '=', 'loueur_wilaya.loueur_id')
                ->where('vehicles.is_active', true)
                ->where('vehicles.status', 'available')
                ->where('loueurs.is_active', true)
                ->where('loueurs.is_suspended', false)
                ->select('loueur_wilaya.wilaya_code', DB::raw('COUNT(DISTINCT vehicles.id) as count'))
                ->groupBy('loueur_wilaya.wilaya_code')
                ->pluck('count', 'wilaya_code')
                ->toArray();

            // Also count loueurs with disponible_national
            $nationalVehicles = Vehicle::whereHas('loueur', function ($q) {
                $q->where('is_active', true)
                  ->where('is_suspended', false)
                  ->where('disponible_national', true);
            })->where('is_active', true)
              ->where('status', 'available')
              ->count();

            // Min price per wilaya
            $minPrices = DB::table('vehicles')
                ->join('loueurs', 'vehicles.loueur_id', '=', 'loueurs.id')
                ->join('loueur_wilaya', 'loueurs.id', '=', 'loueur_wilaya.loueur_id')
                ->where('vehicles.is_active', true)
                ->where('vehicles.status', 'available')
                ->where('loueurs.is_active', true)
                ->where('loueurs.is_suspended', false)
                ->select('loueur_wilaya.wilaya_code', DB::raw('MIN(vehicles.price_per_day) as min_price'))
                ->groupBy('loueur_wilaya.wilaya_code')
                ->pluck('min_price', 'wilaya_code')
                ->toArray();

            $results = [];
            foreach ($wilayas as $code => $name) {
                $count = ($vehicleCounts[$code] ?? 0);
                if ($count === 0 && $nationalVehicles === 0) {
                    continue;
                }

                $coords = $coordinates[$code] ?? null;
                if (!$coords) continue;

                $results[] = [
                    'code' => $code,
                    'name' => $name,
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'vehicles' => $count > 0 ? $count : $nationalVehicles,
                    'min_price' => isset($minPrices[$code]) ? (int) $minPrices[$code] : null,
                    'has_local' => $count > 0,
                ];
            }

            return $results;
        });

        return response()->json(['success' => true, 'data' => $data]);
    }
}
