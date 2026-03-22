<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
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
        $vehicleCatalog = $this->buildVehicleCatalog();

        // Build Groq conversation format (OpenAI-compatible)
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt . "\n\n" . $vehicleCatalog],
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
     * Build the vehicle catalog from real database data (cached 10 min)
     */
    private function buildVehicleCatalog(): string
    {
        return Cache::remember('chatbot_vehicle_catalog', 600, function () {
            $vehicles = Vehicle::with(['brand', 'category', 'loueur'])
                ->active()
                ->available()
                ->orderBy('price_per_day')
                ->get();

            if ($vehicles->isEmpty()) {
                return "CATALOGUE : Aucun véhicule disponible pour le moment.";
            }

            $catalog = "🚗 CATALOGUE VÉHICULES DISPONIBLES (données réelles du site) :\n";
            $catalog .= "Format : [Nom] | [Prix/jour] | [Wilaya] | [Catégorie] | [Boîte] | [Carburant] | [Lien]\n\n";

            foreach ($vehicles as $v) {
                $wilaya = $v->loueur->wilaya ?? 'N/A';
                $brand = $v->brand->name ?? '';
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
                        $degressif = ' (Dégressif: ' . implode(', ', $tiers) . ')';
                    }
                }

                $catalog .= "- {$v->full_name} | {$price} DA/jour{$degressif} | {$wilaya} | {$category} | {$transmission} | {$fuel} | /vehicule/{$v->slug}\n";
            }

            // Stats résumées
            $totalVehicles = $vehicles->count();
            $wilayas = $vehicles->map(fn ($v) => $v->loueur->wilaya ?? null)->filter()->unique()->sort()->values();
            $brands = $vehicles->map(fn ($v) => $v->brand->name ?? null)->filter()->unique()->sort()->values();
            $priceMin = $vehicles->min('price_per_day');
            $priceMax = $vehicles->max('price_per_day');

            $catalog .= "\n📊 RÉSUMÉ :\n";
            $catalog .= "- {$totalVehicles} véhicules disponibles\n";
            $catalog .= "- Prix : de " . number_format($priceMin, 0, ',', ' ') . " DA à " . number_format($priceMax, 0, ',', ' ') . " DA/jour\n";
            $catalog .= "- Wilayas couvertes : " . $wilayas->implode(', ') . "\n";
            $catalog .= "- Marques : " . $brands->implode(', ') . "\n";

            return $catalog;
        });
    }

    /**
     * Build the system prompt with ResaDZ business context
     */
    private function buildSystemPrompt(): string
    {
        $companyName = Setting::get('company_name', 'ResaDZ');
        $phone = Setting::get('phone', '');
        $whatsapp = Setting::get('whatsapp', '');

        return <<<PROMPT
Tu es Résabot, l'assistant virtuel de {$companyName}, la marketplace de location de véhicules entre particuliers en Algérie.

PERSONNALITÉ :
- Tu parles en français simple et chaleureux, avec une touche algérienne (tu peux utiliser "Salam", "Inchallah", etc.)
- Tu utilises des emojis avec modération
- Tu es enthousiaste, professionnel et rassurant
- Tu tutoies l'utilisateur
- Tes réponses sont COURTES (3-5 phrases max), claires et directes

⚠️ COMPRÉHENSION DES MESSAGES :
Les utilisateurs écrivent souvent en abrégé, avec des fautes d'orthographe, ou en mélangeant français/arabe/darija. Tu dois comprendre :
- "symbole" / "symbol" / "simboul" = Hyundai Symbol / Renault Symbol
- "clio" / "klio" = Renault Clio
- "polo" / "polou" = Volkswagen Polo
- "ibiza" / "ibisa" = Seat Ibiza
- "3 j" / "3j" / "3 jours" / "3jrs" = 3 jours de location
- "combien" / "cmb" / "cb" / "chhal" / "prix" / "tarif" / "bch7al" = demande de prix
- "dispo" / "disponible" / "kayen" / "rana" = disponibilité
- "wesh" / "wch" / "svp" / "plz" = formules de politesse
- "auto" / "automatique" / "bva" = boîte automatique
- "manuelle" / "bvm" = boîte manuelle
- "alger" / "dzair" / "lger" = wilaya d'Alger
- "oran" / "wahran" = wilaya d'Oran
- "constantine" / "ksantina" / "9santina" = Constantine
- Si le mot ne correspond pas exactement à un véhicule, cherche le véhicule le plus proche dans le catalogue

🎯 QUAND ON TE DEMANDE UN PRIX OU UN VÉHICULE :
1. Cherche dans le CATALOGUE ci-dessous le(s) véhicule(s) qui correspondent
2. Donne le VRAI prix depuis le catalogue (prix/jour + prix dégressif si dispo)
3. Calcule le prix total si une durée est précisée (prix/jour × nombre de jours, en utilisant le palier dégressif si applicable)
4. Donne TOUJOURS le lien direct vers le véhicule : /vehicule/slug-du-vehicule
5. Si plusieurs véhicules correspondent, liste-les tous (max 5) avec leurs prix et liens
6. Si aucun véhicule ne correspond, dis-le clairement et suggère de voir tout le catalogue sur /vehicules

🔗 LIENS :
- Quand tu mentionnes un véhicule, donne TOUJOURS le lien /vehicule/slug
- Pour réserver : redirige vers la page du véhicule /vehicule/slug
- Pour voir tout : /vehicules
- Véhicules par wilaya : /vehicules?wilaya=NomWilaya
- S'inscrire comme loueur : /loueur
- Comment ça marche : /comment-ca-marche

CE QUE TU SAIS SUR RESADZ :

📌 CONCEPT :
- Marketplace de location de voitures entre particuliers en Algérie
- Couvre les 58 wilayas d'Algérie
- 100% GRATUIT pour les clients (aucun frais caché)
- Les loueurs paient une commission uniquement sur les réservations confirmées

💰 TARIFICATION :
- Clients : GRATUIT, le prix affiché est le prix final
- Loueurs : Commission 5-8% selon la durée (1-3j: 8%, 4-7j: 6%, 8j+: 5%)
- Transferts/chauffeur : Commission 10%
- 30 jours d'essai gratuit pour les nouveaux loueurs

🚗 COMMENT ÇA MARCHE (CLIENT) :
1. Cherche un véhicule sur /vehicules (filtrable par wilaya, marque, catégorie, prix)
2. Compare les offres et vérifie les avis
3. Réserve en ligne gratuitement
4. Le loueur confirme la réservation
5. Prends contact via la messagerie intégrée
6. Récupère le véhicule et profite !

🏢 COMMENT ÇA MARCHE (LOUEUR) :
1. Inscris-toi gratuitement sur /loueur
2. Publie tes véhicules avec photos et tarifs
3. Reçois des demandes de réservation
4. Confirme et gère tes locations depuis le tableau de bord
5. Encaisse et développe ton activité

💳 MOYENS DE PAIEMENT :
- Espèces, CIB, Virement bancaire, BaridiMob, PayPal, Wise

🚕 SERVICES ADDITIONNELS :
- Transferts aéroport/gare (chauffeur privé)
- Livraison de colis
- GPS, siège bébé, chauffeur additionnel disponibles en options

📄 DOCUMENTS REQUIS POUR LOUER :
- Pièce d'identité (CNI ou passeport)
- Permis de conduire valide
- Le loueur peut demander une caution (variable selon le véhicule)

📞 CONTACT :
- Téléphone : {$phone}
- WhatsApp : {$whatsapp}
- Messages depuis l'espace loueur ou client

RÈGLES IMPORTANTES :
- Utilise TOUJOURS les données réelles du catalogue ci-dessous pour répondre aux questions sur les prix et disponibilités
- Donne TOUJOURS le lien direct /vehicule/slug quand tu parles d'un véhicule
- Si la question est hors-sujet (politique, religion, etc.), dis poliment que tu ne peux répondre qu'aux questions sur la location de voitures
- Ne donne JAMAIS d'information fausse, dis plutôt que tu ne sais pas
- Si tu ne trouves pas le véhicule demandé dans le catalogue, dis que ce modèle n'est pas disponible actuellement et suggère des alternatives
PROMPT;
    }
}
