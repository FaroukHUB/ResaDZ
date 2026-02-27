<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use App\Models\TransferBooking;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Page de recherche de transferts
     */
    public function search(Request $request)
    {
        $departure = $request->input('departure', '');
        $destination = $request->input('destination', '');
        $date = $request->input('date', '');
        $time = $request->input('time', '');
        $passengers = (int) $request->input('passengers', 1);

        // Chercher les loueurs qui proposent le service de transfert
        $loueurs = Loueur::where('is_active', true)
            ->whereHas('settings', function ($q) {
                $q->where('key', 'transfer_enabled')->where('value', 'true');
            })
            ->with('user')
            ->get();

        // Filtrer et enrichir avec les routes correspondantes
        $results = [];

        foreach ($loueurs as $loueur) {
            $routes = $loueur->getSetting('transfer_routes', []);
            $airports = $loueur->getSetting('transfer_airports', []);
            $cities = $loueur->getSetting('transfer_cities', []);
            $description = $loueur->getSetting('transfer_description', '');
            $is24h = $loueur->getSetting('transfer_24h', false);
            $luggageIncluded = $loueur->getSetting('transfer_luggage', true);

            // Chercher les routes qui correspondent à la recherche
            $matchingRoutes = [];
            foreach ($routes as $route) {
                $fromMatch = empty($departure) || $this->fuzzyMatch($departure, $route['from'] ?? '');
                $toMatch = empty($destination) || $this->fuzzyMatch($destination, $route['to'] ?? '');
                $passengersOk = $passengers <= ($route['max_passengers'] ?? 4);

                if ($fromMatch && $toMatch && $passengersOk) {
                    $matchingRoutes[] = $route;
                }
            }

            // Si pas de route exacte mais le loueur dessert la zone, l'inclure quand même
            if (empty($matchingRoutes) && !empty($departure)) {
                $departureLower = mb_strtolower($departure);
                $zoneMatch = false;

                // Check airports
                foreach ($airports as $airport) {
                    if ($this->fuzzyMatch($departure, $airport)) {
                        $zoneMatch = true;
                        break;
                    }
                }

                // Check cities
                if (!$zoneMatch) {
                    foreach ($cities as $city) {
                        if ($this->fuzzyMatch($departure, $city) || $this->fuzzyMatch($destination, $city)) {
                            $zoneMatch = true;
                            break;
                        }
                    }
                }

                if ($zoneMatch) {
                    $matchingRoutes[] = [
                        'from' => $departure,
                        'to' => $destination ?: 'Sur demande',
                        'price' => 0,
                        'vehicle_type' => 'berline',
                        'max_passengers' => 4,
                        'on_request' => true,
                    ];
                }
            }

            if (!empty($matchingRoutes) || (empty($departure) && empty($destination))) {
                // Si pas de recherche spécifique, montrer toutes les routes
                if (empty($departure) && empty($destination)) {
                    $matchingRoutes = $routes;
                }

                $results[] = [
                    'loueur' => $loueur,
                    'routes' => $matchingRoutes,
                    'description' => $description,
                    'is_24h' => $is24h,
                    'luggage_included' => $luggageIncluded,
                    'rating' => $loueur->rating ?? 0,
                    'review_count' => $loueur->reviews_count ?? 0,
                ];
            }
        }

        // Trier par note décroissante
        usort($results, fn($a, $b) => $b['rating'] <=> $a['rating']);

        return view('front.pages.transfers', compact(
            'results', 'departure', 'destination', 'date', 'time', 'passengers'
        ));
    }

    /**
     * Réserver un transfert
     */
    public function book(Request $request)
    {
        $request->validate([
            'loueur_id' => 'required|exists:loueurs,id',
            'departure' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'transfer_date' => 'required|date|after_or_equal:today',
            'transfer_time' => 'required|string',
            'passengers' => 'required|integer|min:1|max:20',
            'luggage_count' => 'required|integer|min:0|max:20',
            'price' => 'nullable|numeric|min:0',
            'vehicle_type' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'required|email|max:255',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        $transfer = TransferBooking::create([
            'loueur_id' => $request->loueur_id,
            'departure' => $request->departure,
            'destination' => $request->destination,
            'transfer_date' => $request->transfer_date,
            'transfer_time' => $request->transfer_time,
            'passengers' => $request->passengers,
            'luggage_count' => $request->luggage_count,
            'price' => $request->price ?? 0,
            'vehicle_type' => $request->vehicle_type,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'client_email' => $request->client_email,
            'client_notes' => $request->client_notes,
        ]);

        return redirect()->route('transfers.confirmation', $transfer->reference);
    }

    /**
     * Page de confirmation
     */
    public function confirmation(string $reference)
    {
        $transfer = TransferBooking::with('loueur')
            ->where('reference', $reference)
            ->firstOrFail();

        return view('front.pages.transfer-confirmation', compact('transfer'));
    }

    /**
     * Correspondance floue entre 2 chaînes
     */
    private function fuzzyMatch(string $search, string $target): bool
    {
        $search = mb_strtolower(trim($search));
        $target = mb_strtolower(trim($target));

        if (empty($search) || empty($target)) return false;

        // Correspondance directe
        if (str_contains($target, $search) || str_contains($search, $target)) {
            return true;
        }

        // Mots communs
        $searchWords = explode(' ', $search);
        foreach ($searchWords as $word) {
            if (mb_strlen($word) >= 3 && str_contains($target, $word)) {
                return true;
            }
        }

        return false;
    }
}
