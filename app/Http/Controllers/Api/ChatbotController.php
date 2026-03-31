<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\ChauffeurVehicle;
use App\Models\Loueur;
use App\Models\Review;
use App\Models\Setting;
use App\Models\TransferRoute;
use App\Models\Vehicle;
use App\Models\VehicleOffer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public const CACHE_KEY = 'chatbot_knowledge_base';
    public const CACHE_TTL = 300; // 5 min (contient des données de disponibilité)

    /**
     * Handle chatbot message via Groq API (free)
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
        ]);

        $apiKey = config('services.groq.api_key');

        if (! $apiKey) {
            return response()->json([
                'reply' => "Désolé, le chatbot n'est pas encore configuré. Contactez-nous directement via la page de contact !",
            ]);
        }

        $systemPrompt = $this->buildSystemPrompt();
        $knowledgeBase = $this->buildKnowledgeBase();

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt . "\n\n" . $knowledgeBase],
        ];

        if ($request->history) {
            foreach ($request->history as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $msg['content'],
                    ];
                }
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $request->message,
        ];

        $model = config('services.groq.model', 'llama-3.3-70b-versatile');

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content']
                    ?? "Désolé, je n'ai pas compris. Reformule ta question !";

                return response()->json(['reply' => trim($reply)]);
            }

            Log::warning('Chatbot Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $model,
            ]);

            return response()->json([
                'reply' => "Oups, j'ai un petit souci technique. Réessaie dans un instant !",
            ]);
        } catch (\Exception $e) {
            Log::error('Chatbot exception', ['error' => $e->getMessage()]);

            return response()->json([
                'reply' => "Désolé, je suis temporairement indisponible. Tu peux nous contacter directement !",
            ]);
        }
    }

    /**
     * Clear the chatbot knowledge cache (called by observers)
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Build the full knowledge base from all database data
     */
    private function buildKnowledgeBase(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $kb = '';
            $kb .= $this->buildVehicleCatalog();
            $kb .= "\n\n";
            $kb .= $this->buildTransferCatalog();
            $kb .= "\n\n";
            $kb .= $this->buildLoueurDirectory();
            $kb .= "\n\n";
            $kb .= $this->buildActiveOffers();
            $kb .= "\n\n";
            $kb .= $this->buildReviewsSummary();

            return $kb;
        });
    }

    /**
     * Vehicle catalog with real prices and details
     */
    private function buildVehicleCatalog(): string
    {
        $vehicles = Vehicle::with(['brand', 'category', 'loueur', 'availabilities', 'bookings'])
            ->active()
            ->available()
            ->orderBy('price_per_day')
            ->get();

        $baseUrl = rtrim(config('app.url', 'https://resadz.com'), '/');

        if ($vehicles->isEmpty()) {
            return "🚗 CATALOGUE VÉHICULES : Aucun véhicule disponible pour le moment.";
        }

        $catalog = "🚗 CATALOGUE VÉHICULES DISPONIBLES ({$vehicles->count()} véhicules) :\n";

        foreach ($vehicles as $v) {
            $wilaya = $v->loueur->wilaya ?? 'N/A';
            $loueurName = $v->loueur->company_name ?? '';
            $category = $v->category->name ?? '';
            $transmission = $v->transmission === 'automatic' ? 'Auto' : 'Manuelle';
            $fuel = match ($v->fuel_type) {
                'diesel' => 'Diesel',
                'essence' => 'Essence',
                'hybrid' => 'Hybride',
                'electric' => 'Électrique',
                default => $v->fuel_type,
            };
            $price = number_format($v->price_per_day, 0, ',', ' ');
            $seats = $v->seats ?? 5;
            $year = $v->year ?? '';
            $clim = $v->has_air_conditioning ? 'Clim' : 'Sans clim';

            // Prix dégressifs
            $degressif = '';
            if ($v->degressive_pricing && is_array($v->degressive_pricing)) {
                $tiers = [];
                foreach ($v->degressive_pricing as $tier) {
                    if (isset($tier['from_days']) && isset($tier['price_per_day'])) {
                        $tiers[] = $tier['from_days'] . 'j+: ' . number_format($tier['price_per_day'], 0, ',', ' ') . ' DA';
                    }
                }
                if ($tiers) {
                    $degressif = ' | Dégressif: ' . implode(', ', $tiers);
                }
            }

            // Caution
            $caution = $v->deposit_amount ? ' | Caution: ' . number_format($v->deposit_amount, 0, ',', ' ') . ' DA' : '';

            // Min/max jours
            $duree = '';
            if ($v->min_rental_days > 1) {
                $duree .= " | Min {$v->min_rental_days}j";
            }
            if ($v->max_rental_days) {
                $duree .= " | Max {$v->max_rental_days}j";
            }

            // Dates bloquées (availabilities + bookings confirmés/actifs)
            $blockedPeriods = [];
            $now = now();
            $horizon = $now->copy()->addDays(30);

            foreach ($v->availabilities as $avail) {
                if ($avail->end_date >= $now && $avail->start_date <= $horizon) {
                    $blockedPeriods[] = $avail->start_date->format('d/m') . '-' . $avail->end_date->format('d/m');
                }
            }

            foreach ($v->bookings as $booking) {
                if (in_array($booking->status, ['confirmed', 'active']) && $booking->end_date >= $now && $booking->start_date <= $horizon) {
                    $blockedPeriods[] = $booking->start_date->format('d/m') . '-' . $booking->end_date->format('d/m');
                }
            }

            $availStr = '';
            if (!empty($blockedPeriods)) {
                $availStr = ' | ⛔ INDISPONIBLE: ' . implode(', ', $blockedPeriods);
            }

            $catalog .= "- {$v->full_name} ({$year}) | {$price} DA/jour{$degressif} | {$wilaya} | {$category} | {$transmission} | {$fuel} | {$seats} places | {$clim}{$caution}{$duree} | Loueur: {$loueurName}{$availStr} | {$baseUrl}/vehicule/{$v->slug}\n";
        }

        // Stats
        $wilayas = $vehicles->map(fn ($v) => $v->loueur->wilaya ?? null)->filter()->unique()->sort()->values();
        $brands = $vehicles->map(fn ($v) => $v->brand->name ?? null)->filter()->unique()->sort()->values();
        $priceMin = $vehicles->min('price_per_day');
        $priceMax = $vehicles->max('price_per_day');

        $catalog .= "\n📊 STATS VÉHICULES : {$vehicles->count()} dispo | ";
        $catalog .= number_format($priceMin, 0, ',', ' ') . " - " . number_format($priceMax, 0, ',', ' ') . " DA/jour | ";
        $catalog .= "Wilayas: " . $wilayas->implode(', ') . " | ";
        $catalog .= "Marques: " . $brands->implode(', ');

        return $catalog;
    }

    /**
     * Transfer/chauffeur routes and vehicles
     */
    private function buildTransferCatalog(): string
    {
        $routes = TransferRoute::with('loueur')
            ->where('is_active', true)
            ->get();

        $chauffeurVehicles = ChauffeurVehicle::with('loueur')
            ->where('is_active', true)
            ->get();

        if ($routes->isEmpty() && $chauffeurVehicles->isEmpty()) {
            return "🚕 TRANSFERTS/CHAUFFEURS : Aucun service disponible pour le moment.";
        }

        $catalog = "🚕 SERVICES TRANSFERT & CHAUFFEUR :\n";

        if ($routes->isNotEmpty()) {
            $catalog .= "\nTRAJETS DISPONIBLES :\n";
            foreach ($routes as $route) {
                $loueurName = $route->loueur->company_name ?? '';
                $price = number_format($route->price, 0, ',', ' ');
                $roundTrip = $route->round_trip && $route->round_trip_price
                    ? ' | A/R: ' . number_format($route->round_trip_price, 0, ',', ' ') . ' DA'
                    : '';
                $type = $route->vehicle_type ?? '';
                $pax = $route->max_passengers ? " | Max {$route->max_passengers} passagers" : '';

                $catalog .= "- {$route->departure} → {$route->destination} | {$price} DA{$roundTrip} | {$type}{$pax} | Chauffeur: {$loueurName}\n";
            }
        }

        if ($chauffeurVehicles->isNotEmpty()) {
            $catalog .= "\nVÉHICULES CHAUFFEUR DISPONIBLES :\n";
            foreach ($chauffeurVehicles as $cv) {
                $loueurName = $cv->loueur->company_name ?? '';
                $wilaya = $cv->loueur->wilaya ?? '';
                $equipments = [];
                if ($cv->has_air_conditioning) $equipments[] = 'Clim';
                if ($cv->has_wifi) $equipments[] = 'WiFi';
                if ($cv->has_child_seat) $equipments[] = 'Siège bébé';
                if ($cv->has_wheelchair_access) $equipments[] = 'Accès PMR';
                $equip = $equipments ? ' | ' . implode(', ', $equipments) : '';

                $catalog .= "- {$cv->brand} {$cv->model} ({$cv->year}) | {$cv->seats} places | {$cv->vehicle_type}{$equip} | {$wilaya} | Chauffeur: {$loueurName}\n";
            }
        }

        return $catalog;
    }

    /**
     * Loueur directory with ratings and services
     */
    private function buildLoueurDirectory(): string
    {
        // Loueurs with available vehicles
        $vehicleLoueurs = Loueur::where('is_active', true)
            ->where('is_suspended', false)
            ->whereNotNull('onboarding_completed_at')
            ->withCount(['vehicles' => function ($q) {
                $q->where('is_active', true)->where('status', 'available');
            }])
            ->having('vehicles_count', '>', 0)
            ->get();

        // Taxi/chauffeur accounts
        $taxiLoueurs = Loueur::where('is_active', true)
            ->where('is_suspended', false)
            ->where('account_type', 'taxi')
            ->get();

        $loueurs = $vehicleLoueurs->merge($taxiLoueurs)->unique('id');

        if ($loueurs->isEmpty()) {
            return "🏢 LOUEURS : Aucun loueur actif pour le moment.";
        }

        $catalog = "🏢 LOUEURS & CHAUFFEURS ACTIFS ({$loueurs->count()}) :\n";

        foreach ($loueurs as $l) {
            $type = $l->account_type === 'taxi' ? 'Chauffeur' : 'Loueur';
            $rating = $l->rating ? "★ {$l->rating}/5 ({$l->total_reviews} avis)" : 'Nouveau';
            $verified = $l->is_verified ? '✓ Vérifié' : '';
            $services = [];
            if ($l->offers_transfer) $services[] = 'Transferts';
            if ($l->offers_delivery) $services[] = 'Livraison';
            $servicesStr = $services ? ' | Services: ' . implode(', ', $services) : '';
            $vehicleCount = $l->vehicles_count ?? 0;
            $vStr = $vehicleCount > 0 ? " | {$vehicleCount} véhicules" : '';

            $catalog .= "- {$l->company_name} ({$type}) | {$l->wilaya} | {$rating} {$verified}{$vStr}{$servicesStr}\n";
        }

        return $catalog;
    }

    /**
     * Active promotions and offers
     */
    private function buildActiveOffers(): string
    {
        $baseUrl = rtrim(config('app.url', 'https://resadz.com'), '/');

        $offers = VehicleOffer::with(['vehicle', 'vehicle.brand'])
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        if ($offers->isEmpty()) {
            return "🏷️ PROMOS : Aucune promotion en cours.";
        }

        $catalog = "🏷️ PROMOTIONS EN COURS ({$offers->count()}) :\n";

        foreach ($offers as $offer) {
            $vehicleName = $offer->vehicle->full_name ?? 'Véhicule';
            $slug = $offer->vehicle->slug ?? '';
            $discount = $offer->discount_type === 'percentage'
                ? "-{$offer->discount_value}%"
                : "-" . number_format($offer->discount_value, 0, ',', ' ') . " DA";
            $badge = $offer->badge_text ?? $offer->title;
            $endDate = $offer->end_date?->format('d/m/Y') ?? '';

            $catalog .= "- {$vehicleName} : {$discount} ({$badge}) | Jusqu'au {$endDate} | {$baseUrl}/vehicule/{$slug}\n";
        }

        return $catalog;
    }

    /**
     * Reviews summary per loueur
     */
    private function buildReviewsSummary(): string
    {
        $reviews = Review::with('loueur')
            ->where('is_public', true)
            ->where('is_approved', true)
            ->where('type', 'client_to_loueur')
            ->latest()
            ->limit(30)
            ->get();

        if ($reviews->isEmpty()) {
            return "⭐ AVIS CLIENTS : Pas encore d'avis publiés.";
        }

        $catalog = "⭐ DERNIERS AVIS CLIENTS :\n";

        // Résumé par loueur
        $byLoueur = $reviews->groupBy(fn ($r) => $r->loueur->company_name ?? 'Inconnu');
        foreach ($byLoueur as $loueurName => $loueurReviews) {
            $avgRating = round($loueurReviews->avg('rating_overall'), 1);
            $count = $loueurReviews->count();
            $lastComment = $loueurReviews->first()->comment ?? '';
            $shortComment = mb_strlen($lastComment) > 80 ? mb_substr($lastComment, 0, 80) . '...' : $lastComment;

            $catalog .= "- {$loueurName} : ★ {$avgRating}/5 ({$count} avis)";
            if ($shortComment) {
                $catalog .= " | Dernier: \"{$shortComment}\"";
            }
            $catalog .= "\n";
        }

        return $catalog;
    }

    /**
     * Build the system prompt with ResaDZ business context
     */
    private function buildSystemPrompt(): string
    {
        $companyName = Setting::get('company_name', 'ResaDZ');
        $phone = Setting::get('phone', '');
        $whatsapp = Setting::get('whatsapp', '');

        $now = now();
        $dateStr = $now->translatedFormat('l j F Y');
        $timeStr = $now->format('H:i');
        $baseUrl = rtrim(config('app.url', 'https://resadz.com'), '/');

        return <<<PROMPT
Tu es Résabot, le CERVEAU de {$companyName}. Tu connais TOUT le site : chaque véhicule, chaque prix, chaque loueur, chaque chauffeur, chaque promo, chaque avis client, et surtout la DISPONIBILITÉ en temps réel de chaque véhicule. Tes données sont mises à jour en temps réel.

📅 DATE ET HEURE ACTUELLES : {$dateStr}, {$timeStr} (heure d'Algérie, UTC+1)
Tu connais la date du jour. Utilise-la pour répondre aux questions de disponibilité ("demain", "ce weekend", "la semaine prochaine", etc.)

PERSONNALITÉ :
- Tu parles en français simple et chaleureux, avec une touche algérienne
- Tu utilises des emojis avec modération
- Tu es enthousiaste, professionnel et rassurant
- Tu tutoies l'utilisateur
- Tes réponses sont COURTES (3-5 phrases max), claires et directes
- IMPORTANT : Ne dis "Salam" que dans ton PREMIER message de la conversation. Ensuite, commence directement par ta réponse sans salutation répétée. Si l'utilisateur te dit "Salam", tu peux répondre "Wa alaikum salam" UNE SEULE FOIS

⚠️ COMPRÉHENSION DES MESSAGES :
Les utilisateurs écrivent souvent en abrégé, avec des fautes, ou en mélangeant français/arabe/darija. Tu DOIS comprendre :
- "symbole" / "symbol" / "simboul" / "simbol" = Renault Symbol ou Hyundai Accent
- "clio" / "klio" / "kliou" = Renault Clio
- "polo" / "polou" = Volkswagen Polo
- "ibiza" / "ibisa" = Seat Ibiza
- "golf" / "golfe" = Volkswagen Golf
- "tucson" / "tukson" / "tokson" = Hyundai Tucson
- "duster" / "daster" = Dacia Duster
- "logan" / "logane" = Dacia Logan
- "3 j" / "3j" / "3 jours" / "3jrs" / "3jour" = 3 jours de location
- "1 semaine" / "7j" / "semaine" = 7 jours
- "1 mois" / "30j" / "mois" = 30 jours
- "combien" / "cmb" / "cb" / "chhal" / "prix" / "tarif" / "bch7al" / "9adach" / "gadech" = demande de prix
- "dispo" / "disponible" / "kayen" / "kayna" / "rana" / "yella" = disponibilité
- "wesh" / "wch" / "svp" / "plz" / "stp" = formules de politesse
- "auto" / "automatique" / "bva" / "otomatik" = boîte automatique
- "manuelle" / "bvm" / "maniel" = boîte manuelle
- "alger" / "dzair" / "lger" / "el djazair" = Alger
- "oran" / "wahran" = Oran
- "constantine" / "ksantina" / "9santina" / "kosantina" = Constantine
- "setif" / "stif" / "s'tif" = Sétif
- "annaba" / "3annaba" / "bône" = Annaba
- "tizi" / "tizi ouzou" / "to" = Tizi Ouzou
- "bejaia" / "bgayet" / "bougie" = Béjaïa
- "blida" / "el boulaida" = Blida
- "transfert" / "taxi" / "chauffeur" / "aéroport" / "airport" = service transfert
- "livraison" / "colis" / "envoi" = service livraison
- "avis" / "note" / "commentaire" / "review" = avis clients
- "promo" / "offre" / "reduction" / "solde" = promotions
- Si le mot ne correspond pas exactement, cherche le véhicule le PLUS PROCHE dans le catalogue

🎯 QUAND ON TE DEMANDE UN PRIX OU UN VÉHICULE :
1. Cherche dans le CATALOGUE ci-dessous le(s) véhicule(s) qui correspondent
2. Donne le VRAI prix depuis le catalogue (prix/jour + prix dégressif si applicable)
3. Calcule le prix total si une durée est précisée (prix/jour × jours, en utilisant le palier dégressif)
4. Mentionne la caution si elle existe
5. Donne TOUJOURS le lien direct : /vehicule/slug-du-vehicule
6. Si plusieurs véhicules correspondent, liste les meilleurs (max 5) avec prix et liens
7. Si une PROMO existe sur ce véhicule, mentionne-la !
8. Mentionne la note du loueur si elle est disponible

🚕 QUAND ON DEMANDE UN TRANSFERT :
1. Cherche dans les TRAJETS DISPONIBLES ci-dessous
2. Donne le prix réel du trajet
3. Mentionne si l'aller-retour est dispo et son prix
4. Indique le type de véhicule et les équipements

⭐ QUAND ON DEMANDE DES AVIS :
1. Donne la note moyenne du loueur depuis les AVIS ci-dessous
2. Cite un avis récent si disponible
3. Rassure sur la fiabilité du loueur

🔗 LIENS À DONNER (TOUJOURS avec le domaine complet) :
- Véhicule spécifique : {$baseUrl}/vehicule/slug-exact (TOUJOURS quand tu parles d'un véhicule, utilise le slug EXACT du catalogue ci-dessous)
- Tous les véhicules : {$baseUrl}/vehicules
- Par wilaya : {$baseUrl}/vehicules?wilaya=NomWilaya
- Par marque : {$baseUrl}/vehicules?marque=NomMarque
- S'inscrire comme loueur : {$baseUrl}/loueur
- Comment ça marche : {$baseUrl}/comment-ca-marche
- Blog : {$baseUrl}/blog
⚠️ N'invente JAMAIS un slug ! Utilise UNIQUEMENT les slugs exacts listés dans le catalogue véhicules ci-dessous

CE QUE TU SAIS SUR RESADZ :

📌 CONCEPT :
- Marketplace de location de voitures entre particuliers en Algérie
- Couvre les 58 wilayas d'Algérie
- 100% GRATUIT pour les clients (aucun frais caché)
- Les loueurs paient une commission uniquement sur les réservations confirmées

💰 TARIFICATION :
- Clients : GRATUIT, le prix affiché est le prix final
- Loueurs : Commission dégressive (1-10j: 8%, +10j: 6%)
- Transferts/chauffeur : Commission 10%
- Inscription ouverte aux loueurs professionnels

🚗 COMMENT ÇA MARCHE (CLIENT) :
1. Cherche un véhicule sur /vehicules
2. Compare les offres et vérifie les avis
3. Réserve en ligne gratuitement
4. Le loueur confirme la réservation
5. Contact via messagerie intégrée
6. Récupère le véhicule !

🏢 COMMENT ÇA MARCHE (LOUEUR) :
1. Inscris-toi gratuitement sur /loueur
2. Publie tes véhicules avec photos et tarifs
3. Reçois des demandes de réservation
4. Gère tout depuis ton tableau de bord

💳 MOYENS DE PAIEMENT : Espèces, CIB, Virement bancaire, BaridiMob, PayPal, En ligne (Wise, Revolut...)

📄 DOCUMENTS REQUIS : CNI ou passeport + Permis de conduire valide

📞 CONTACT :
- Téléphone : {$phone}
- WhatsApp : {$whatsapp}

RÈGLES STRICTES :
- Utilise TOUJOURS les données réelles ci-dessous, JAMAIS d'invention
- Donne TOUJOURS le lien COMPLET ({$baseUrl}/vehicule/slug) quand tu parles d'un véhicule
- N'invente JAMAIS un slug ou un lien — utilise uniquement ceux du catalogue
- Quand on demande la disponibilité, vérifie les DATES BLOQUÉES dans le catalogue (marquées ⛔ INDISPONIBLE). Si un véhicule est bloqué sur la période demandée, dis-le clairement et propose des alternatives
- Tu connais la date d'aujourd'hui ({$dateStr}). Utilise-la pour calculer "demain", "ce weekend", "la semaine prochaine", etc.
- Ne répète PAS "Salam" à chaque message — une seule fois au début de la conversation suffit
- Hors-sujet → dis poliment que tu ne gères que la location de voitures
- Info introuvable → dis que tu ne sais pas et suggère de contacter le support
PROMPT;
    }
}
