<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PanelAssistantController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
            'panel' => 'required|in:loueur,chauffeur,admin',
        ]);

        $apiKey = config('services.groq.api_key');

        if (!$apiKey) {
            return response()->json([
                'reply' => "L'assistant n'est pas encore configuré. Contactez l'équipe ResaDZ.",
            ]);
        }

        $systemPrompt = $this->buildPrompt($request->panel);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        if ($request->history) {
            foreach ($request->history as $msg) {
                if (isset($msg['role'], $msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                        'content' => $msg['content'],
                    ];
                }
            }
        }

        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages' => $messages,
                    'max_tokens' => 600,
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content', "Désolé, je n'ai pas pu répondre.");
                return response()->json(['reply' => $reply]);
            }

            return response()->json(['reply' => "Erreur temporaire. Réessayez dans quelques instants."]);
        } catch (\Exception $e) {
            \Log::warning('Panel assistant error: ' . $e->getMessage());
            return response()->json(['reply' => "Erreur de connexion. Réessayez."]);
        }
    }

    private function buildPrompt(string $panel): string
    {
        $base = "Tu es Résabot, l'assistant intelligent de ResaDZ. Tu aides les utilisateurs à naviguer dans leur espace de gestion. "
            . "Tu réponds en français, mais tu comprends aussi le darija algérien et le franco-arabe (chhal, bch7al, kayen, wesh, kifach, etc.). "
            . "Tu es amical, tu utilises 'Salam' pour saluer, tu tutoies. "
            . "Tes réponses sont courtes et pratiques — tu donnes les étapes exactes avec les noms des menus/boutons. "
            . "Si tu ne connais pas la réponse, dis-le honnêtement et conseille de contacter l'équipe ResaDZ sur WhatsApp.";

        return match ($panel) {
            'loueur' => $base . "\n\n" . $this->getLoueurKnowledge(),
            'chauffeur' => $base . "\n\n" . $this->getChauffeurKnowledge(),
            'admin' => $base . "\n\n" . $this->getAdminKnowledge(),
        };
    }

    private function getLoueurKnowledge(): string
    {
        return <<<'KB'
=== GUIDE PANEL LOUEUR RESADZ ===

## STRUCTURE DU MENU
- **Tableau de bord** : Vue d'ensemble, stats rapides
- **Catalogue → Véhicules** : Liste de tes véhicules, créer/modifier/supprimer
- **Réservations → Mes Réservations** : Toutes les demandes de réservation reçues
- **Finances → Tableau de bord** : Revenus, dépenses, solde (aujourd'hui/semaine/mois)
- **Finances → Transactions** : Historique détaillé des entrées/sorties
- **Configuration → Paramètres** : Profil, zones de livraison, paiements, badges, conditions, notifications
- **Calendrier** : Bloquer/débloquer des dates pour chaque véhicule
- **Messagerie** : Conversations avec les clients

## AJOUTER UN VÉHICULE
1. Va dans Catalogue → Véhicules → bouton "Créer"
2. Remplis : Marque (select) → Modèle (select filtré par marque, tu peux en créer un nouveau) → le Nom complet se remplit automatiquement
3. Année (select), Transmission, Carburant, Couleur (obligatoires)
4. Places, Portes, Bagages (obligatoires)
5. Section Tarification : Prix/jour en DA (obligatoire), prix EUR (optionnel)
6. Section Photos : Si un visuel studio ResaDZ existe pour ta marque/modèle/couleur, il s'affiche automatiquement. Tu peux aussi uploader ta propre photo.
7. Section Badges : Coche les badges qui s'appliquent (assurance, livraison, km illimité, etc.)
8. Clique "Créer"

## VISUELS STUDIO
- ResaDZ fournit des visuels professionnels pour certains modèles (fond showroom, logo ResaDZ)
- Si ton véhicule a un visuel disponible (même marque + modèle + couleur), il est utilisé automatiquement comme photo principale
- Tu peux voir le visuel dans la section Photos quand tu modifies ton véhicule
- Si tu uploades ta propre photo, elle remplace le visuel studio

## GÉRER LES RÉSERVATIONS
- Quand un client réserve, tu reçois un email + notification dans le panel
- Va dans Réservations → clique sur la réservation → tu peux la confirmer ou la refuser
- Statuts : Pending → Confirmed → Active → Completed

## CALENDRIER DE DISPONIBILITÉ
- Va dans Calendrier (menu latéral)
- Sélectionne un véhicule
- Clique sur les dates pour bloquer/débloquer
- Vert = disponible, Rouge = bloqué, Orange = maintenance

## STATUTS VÉHICULE
- Disponible : visible sur le site, réservable
- En location / Réservé : visible sur le site avec badge "En location", non réservable
- En maintenance : masqué du site
- Indisponible : masqué du site

## COMMISSION RESADZ
- Location 1-10 jours : 8% du montant total
- Location +10 jours : 6% du montant total
- Tu ne paies rien sans réservation
- Astuce : ajoute 500 DA à ton tarif habituel pour couvrir la commission

## PRIX DÉGRESSIFS
- Tu peux offrir des réductions pour les locations longue durée
- Exemple : -10% pour 7+ jours, -20% pour 30+ jours
- Va dans la section "Prix dégressifs" quand tu modifies un véhicule

## TARIFS SAISONNIERS
- Ajoute des suppléments pour les périodes de forte demande (été, vacances)
- Exemple : +2000 DA/jour du 1er juillet au 31 août
- Les clients voient le supplément clairement avant de réserver

## PARAMÈTRES IMPORTANTS
- **Profil** : Nom, description, logo, photo de couverture, réseaux sociaux
- **Zones de livraison** : Les wilayas où tu livres + frais de livraison
- **Paiements** : Méthodes acceptées (espèces, CIB, BaridiMob, PayPal...)
- **Acompte** : Pourcentage demandé à la réservation (recommandé 20-30%)
- **Conditions** : Âge min, kilométrage max, caution, interdiction animaux, etc.
- **Badges** : Assurance, livraison, km illimité, aéroport, prix dégressif
- **Notifications** : Email, push, WhatsApp

## MOT DE PASSE OUBLIÉ
- Sur la page de connexion, clique "Mot de passe oublié ?"
- Entre ton email → tu recevras un lien de réinitialisation

## PROBLÈMES FRÉQUENTS
- "Je ne vois pas mon véhicule sur le site" → Vérifie que le statut est "Disponible" et is_active est activé
- "Le client ne peut pas réserver" → Vérifie que le statut n'est pas "En location" ou "Indisponible"
- "Je ne reçois pas les notifications" → Va dans Paramètres → Notifications et vérifie que l'email est activé
KB;
    }

    private function getChauffeurKnowledge(): string
    {
        return <<<'KB'
=== GUIDE PANEL CHAUFFEUR RESADZ ===

## STRUCTURE DU MENU
- **Tableau de bord** : Stats courses, revenus
- **Finances → Tableau de bord** : CA transferts, CA livraisons, dépenses, solde
- **Finances → Transactions** : Historique entrées/sorties
- **Transferts** : Réservations de transfert reçues
- **Livraisons** : Réservations de livraison reçues
- **Véhicule chauffeur** : Ton véhicule de service
- **Options** : Options additionnelles (WiFi, siège bébé, etc.)
- **Paramètres** : Profil, services, trajets, notifications

## COMMISSION
- 10% fixe sur chaque course confirmée
- Les clients te paient directement
- ResaDZ envoie une facture hebdomadaire

## SERVICES DISPONIBLES
- Transferts aéroport/gare
- Courses en ville
- Livraisons de colis

## AJOUTER UN TRAJET
- Va dans tes paramètres ou la section trajets
- Ajoute : ville départ → ville arrivée → prix
- Exemple : Alger centre → Aéroport Houari Boumédiène → 3500 DA

## PARAMÈTRES
- **Profil** : Nom, description, photo, expérience
- **Véhicule** : Marque, modèle, année, photo
- **Options** : WiFi, siège bébé, eau, etc.
- **Notifications** : Email, push, WhatsApp
KB;
    }

    private function getAdminKnowledge(): string
    {
        return <<<'KB'
=== GUIDE PANEL ADMIN RESADZ ===

## STRUCTURE DU MENU
- **Tableau de bord** : KPIs, stats globales
- **Gestion → Loueurs** : Liste loueurs, accepter/refuser, voir détails
- **Gestion → Réservations** : Toutes les réservations de la plateforme
- **Gestion → Prospects (CRM)** : Kanban + liste de prospects
- **Catalogue → Véhicules** : Tous les véhicules
- **Catalogue → Marques** : Gestion des marques
- **Catalogue → Catégories** : Gestion des catégories (Citadine, SUV, Berline...)
- **Catalogue → Modèles véhicules** : Modèles pré-enregistrés par marque
- **Catalogue → Visuels véhicules** : Templates photo (marque + modèle + couleur)
- **Marketing → Statistiques** : Visites, trafic, conversion, géographie
- **Marketing → Blog** : Articles SEO
- **Configuration → Paramètres** : Settings globaux du site

## GÉRER LES LOUEURS
- Liste avec filtres : actif, vérifié, suspendu, en essai
- Bouton "Accepter" (vert) : Active le compte + envoie mail d'activation
- Bouton "Refuser" (rouge) : Suspend le compte avec raison
- Bouton "Accéder au dashboard" : Ouvre le panel loueur

## VISUELS VÉHICULES
- Catalogue → Visuels véhicules
- Ajouter un visuel : Marque + Modèle + Couleur + Image
- Les loueurs qui créent un véhicule correspondant verront ce visuel automatiquement

## MODÈLES VÉHICULES
- Catalogue → Modèles véhicules
- ~200 modèles pré-enregistrés (Renault Clio, Hyundai Tucson, etc.)
- Les loueurs peuvent en créer de nouveaux depuis leur formulaire
- L'admin peut désactiver/supprimer des modèles

## STATISTIQUES
- Marketing → Statistiques
- Visiteurs en temps réel, visites jour/semaine/mois
- Géographie (pays, villes, régions)
- Sources de trafic, appareils, navigateurs
- Funnel de conversion (accueil → liste → détail → réservation)
- Clics sur actions (téléphone, WhatsApp, réservation)
- Données cachées 5 minutes pour la performance

## CRM PROSPECTS
- Gestion → Prospects : Vue Kanban (glisser-déposer)
- 5 colonnes : Non contacté → Contacté → Intéressé → Inscrit → Pas intéressé
- Import CSV en masse
- Filtres par statut, wilaya, source
KB;
    }
}
