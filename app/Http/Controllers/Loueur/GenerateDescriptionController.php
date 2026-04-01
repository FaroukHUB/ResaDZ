<?php

namespace App\Http\Controllers\Loueur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GenerateDescriptionController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'annee_creation' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:100',
            'types_vehicules' => 'nullable|string|max:255',
            'services' => 'nullable|string|max:255',
            'clientele' => 'nullable|string|max:255',
            'avantages' => 'nullable|string|max:255',
        ]);

        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'Service IA non configuré.'], 500);
        }

        $loueur = Auth::user()->loueur;
        $companyName = $loueur?->company_name ?? 'Mon agence';

        $prompt = "Tu es un expert en rédaction de profils d'agences de location de voiture en Algérie.

Rédige une description professionnelle et convaincante pour cette agence :

- Nom de l'agence : {$companyName}
- Créée en : " . ($request->annee_creation ?: 'non précisé') . "
- Basée à : " . ($request->ville ?: 'non précisé') . "
- Types de véhicules : " . ($request->types_vehicules ?: 'non précisé') . "
- Services proposés : " . ($request->services ?: 'non précisé') . "
- Clientèle cible : " . ($request->clientele ?: 'non précisé') . "
- Points forts : " . ($request->avantages ?: 'non précisé') . "
- Plateforme : ResaDZ

La description doit :
- Faire 3 à 4 phrases maximum
- Être rédigée à la 3ème personne (l'agence, pas nous)
- Inspirer confiance aux clients
- Mentionner la localisation si fournie
- Être en français
- Ne pas utiliser de superlatifs comme 'meilleur' ou 'numéro 1'
- Sonner humaine et authentique, pas publicitaire
- Ne pas commencer par 'Bienvenue'

Réponds uniquement avec la description, sans introduction ni commentaire.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(15)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 300,
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Erreur API, réessayez.'], 500);
            }

            $description = $response->json('choices.0.message.content', '');
            $description = trim(str_replace(['"', '«', '»'], '', $description));

            return response()->json(['description' => $description]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Génération impossible, réessayez dans quelques instants.'], 500);
        }
    }
}
