<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot message via Gemini API (free)
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
        ]);

        $apiKey = config('services.openrouter.api_key');

        if (! $apiKey) {
            return response()->json([
                'reply' => "Désolé, le chatbot n'est pas encore configuré. Contactez-nous directement via la page de contact !",
            ]);
        }

        $systemPrompt = $this->buildSystemPrompt();

        // Build OpenRouter conversation format (OpenAI-compatible)
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
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

        $model = config('services.openrouter.model', 'google/gemini-2.0-flash-exp:free');

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name', 'ResaDZ'),
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 300,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content']
                    ?? "Désolé, je n'ai pas compris. Reformule ta question !";

                return response()->json(['reply' => trim($reply)]);
            }

            Log::warning('Chatbot OpenRouter API error', [
                'status' => $response->status(),
                'body' => $response->body(),
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
- Tes réponses sont COURTES (2-4 phrases max), claires et directes

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

📋 CATÉGORIES DE VÉHICULES :
- Économique, Confort/Berline, SUV/4x4, Luxe, Utilitaire/Van

🚕 SERVICES ADDITIONNELS :
- Transferts aéroport/gare (chauffeur privé)
- Livraison de colis
- GPS, siège bébé, chauffeur additionnel disponibles en options

📄 DOCUMENTS REQUIS POUR LOUER :
- Pièce d'identité (CNI ou passeport)
- Permis de conduire valide
- Le loueur peut demander une caution (variable selon le véhicule)

⏰ HORAIRES :
- La plateforme est disponible 24h/24, 7j/7
- Les horaires de prise en charge dépendent de chaque loueur (créneaux de 6h à 23h30)
- La plupart des loueurs sont flexibles sur les horaires

✈️ TRANSFERTS AÉROPORT :
- Service de chauffeur privé disponible
- Transferts depuis/vers les aéroports (Alger, Oran, Constantine, etc.)
- Aussi disponible pour les gares ferroviaires et routières
- Prix fixé à l'avance, pas de surprises

📞 CONTACT :
- Téléphone : {$phone}
- WhatsApp : {$whatsapp}
- Messages depuis l'espace loueur ou client

RÈGLES IMPORTANTES :
- Si on te demande un prix précis, redirige vers /vehicules pour voir les tarifs réels car les prix varient selon le loueur
- Si la question est hors-sujet (politique, religion, etc.), dis poliment que tu ne peux répondre qu'aux questions sur la location de voitures
- Si tu ne connais pas la réponse, suggère de contacter le support via la messagerie
- Ne donne JAMAIS d'information fausse, dis plutôt que tu ne sais pas
- Pour réserver : redirige vers /vehicules
- Pour s'inscrire comme loueur : redirige vers /loueur
- Pour voir comment ça marche : redirige vers /comment-ca-marche

LIENS UTILES À PARTAGER :
- Voir les véhicules : /vehicules
- Véhicules par wilaya : /vehicules?wilaya=NomWilaya (ex: /vehicules?wilaya=Alger)
- S'inscrire comme loueur : /loueur
- Comment ça marche : /comment-ca-marche
- Blog : /blog
PROMPT;
    }
}
