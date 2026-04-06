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
=== GUIDE COMPLET PANEL LOUEUR RESADZ ===

## STRUCTURE DU MENU
- **Tableau de bord** : Vue d'ensemble, stats rapides
- **Catalogue → Véhicules** : Liste de tes véhicules, créer/modifier/supprimer
- **Réservations → Mes Réservations** : Toutes les demandes de réservation reçues
- **Finances → Tableau de bord** : Revenus vs dépenses (courbe 30 jours), solde jour/semaine/mois
- **Finances → Transactions** : Historique détaillé des entrées/sorties
- **Configuration → Paramètres** : Profil, zones, paiements, badges, conditions, notifications
- **Configuration → Acomptes** : Pourcentage d'acompte + méthodes de paiement + délais
- **Calendrier** : Bloquer/débloquer des dates pour chaque véhicule
- **Messagerie** : Conversations avec les clients

## ONBOARDING (PREMIÈRE CONNEXION)
8 étapes à suivre dans l'ordre :
1. CGU & Contrat : Lire et accepter les conditions + contrat de partenariat
2. Profil : Nom, description, téléphone, WhatsApp, email, adresse, wilaya
3. Zones : Wilayas où tu livres + frais de livraison par zone
4. Paiements : Méthodes acceptées (espèces, CIB, BaridiMob, PayPal...) + pourcentage d'acompte
5. Options : Options payantes ou gratuites (GPS, siège bébé, etc.)
6. Conditions : Âge min, km max, caution, pas de fumeur, etc.
7. Badges : Assurance, livraison, km illimité, aéroport, dégressif + badges personnalisés
8. Notifications : Email, push, WhatsApp
Tu peux modifier tout ça plus tard dans Paramètres.

## AJOUTER UN VÉHICULE
1. Catalogue → Véhicules → bouton "Créer"
2. Marque (select) → Modèle (select filtré par marque, tu peux en créer un nouveau si absent) → Nom complet se remplit automatiquement
3. Année (select 2005-2027), Transmission, Carburant, Couleur → tous obligatoires
4. Places, Portes, Bagages → obligatoires
5. Catégorie : Citadine, SUV, Berline, etc.
6. Section Tarification : Prix/jour en DA (obligatoire), prix EUR (optionnel pour la diaspora)
7. Section Prix dégressifs : Réductions longue durée (ex: -10% pour 7+ jours)
8. Section Tarifs saisonniers : Suppléments pour haute saison (ex: +2000 DA/jour en été)
9. Section Photos : Si un visuel studio ResaDZ existe pour ta marque/modèle/couleur, il est utilisé automatiquement. Tu peux uploader ta propre photo.
10. Section Badges : Assurance, livraison, km illimité, aéroport, dégressif + badges personnalisés
11. Section Options : GPS, siège bébé, conducteur additionnel, etc. avec prix
12. Section Frais de retour : Frais si véhicule rendu sans plein ou sans lavage
Chaque section a une bordure colorée à gauche pour s'y retrouver.

## VISUELS STUDIO
- ResaDZ fournit des visuels professionnels pour certains modèles (fond showroom, logo ResaDZ, nom du loueur en overlay 3D)
- Si ton véhicule a un visuel disponible (même marque + modèle + couleur), il est utilisé automatiquement
- En mode édition, tu verras le visuel dans la section Photos avec un encadré vert
- Si tu uploades ta propre photo, elle remplace le visuel studio
- Les visuels sont gérés par l'admin dans Catalogue → Visuels véhicules

## SYSTÈME D'ACOMPTE
- Configure le pourcentage dans Configuration → Acomptes (recommandé 20-30%)
- Choisis les méthodes de paiement : espèces, CIB, BaridiMob, PayPal, virement...
- Chaque méthode a un délai en heures (ex: espèces = 4h, PayPal = 48h)
- Quand un client réserve, il choisit comment payer l'acompte
- Un compte à rebours s'affiche sur sa page de confirmation
- Si le client ne paie pas dans le délai → la réservation est automatiquement annulée
- Tu marques manuellement "acompte payé" dans la fiche réservation quand tu reçois le paiement
- Exemple : total 10 000 DA, acompte 30% = 3 000 DA à verser

## GÉRER LES RÉSERVATIONS
- Quand un client réserve, tu reçois un email + notification dans le panel
- Va dans Réservations → clique sur la réservation
- Tu peux la confirmer, la refuser, voir les documents du client
- Statuts : Pending (en attente) → Confirmed (confirmée) → Active (en cours) → Completed (terminée)
- Quand le client envoie ses documents (CNI, permis), tu reçois une notification email
- Onglet Paiement dans la réservation : acompte, caution, montant payé, reste à payer

## CALENDRIER DE DISPONIBILITÉ
- Clique sur "Calendrier" dans le menu
- Sélectionne un véhicule en haut
- Clique sur les jours pour bloquer/débloquer
- Le système fusionne automatiquement les jours adjacents bloqués
- Vert = disponible, Rouge = bloqué, Vert foncé = réservé, Orange = maintenance
- Stats mensuelles affichées : jours dispos, bloqués, réservés
- Les clients voient un calendrier de disponibilité sur la fiche véhicule (FullCalendar)

## STATUTS VÉHICULE
- Disponible : visible sur le site, réservable
- Réservé / En location : visible sur le site avec bandeau diagonal rouge "INDISPONIBLE", bouton grisé, non réservable
- En maintenance : masqué du site
- Indisponible : masqué du site

## COMMISSION RESADZ
- Location 1-10 jours : 8% du montant total
- Location +10 jours : 6% du montant total
- Tu ne paies rien sans réservation
- Astuce : ajoute 500 DA à ton tarif habituel pour couvrir la commission
- Exemple : tu vises 6 000 DA nets ? Affiche 6 500 DA

## PRIX DÉGRESSIFS
- Offre des réductions pour les locations longue durée
- Configure dans le formulaire véhicule, section "Prix dégressifs"
- Ajoute : nombre de jours minimum → pourcentage ou montant de réduction
- Les clients voient les prix dégressifs sur la fiche véhicule

## TARIFS SAISONNIERS
- Ajoute des suppléments pour les périodes de forte demande (été, Aïd, vacances)
- Configure dans le formulaire véhicule, section "Tarifs saisonniers"
- Indique : nom de la saison, dates début/fin, supplément en DA/jour
- Les clients voient un badge "Haute saison" sur la fiche véhicule

## FINANCES
- Tableau de bord financier : revenus vs dépenses sur 30 jours (graphique)
- 3 colonnes : Aujourd'hui / Cette semaine / Ce mois avec solde
- Revenus en attente : réservations confirmées pas encore terminées
- Transactions : historique complet, filtrable par type/date
- Tu peux enregistrer tes dépenses (carburant, entretien, etc.)

## CONTRAT PDF
- Un contrat de location PDF est généré automatiquement pour chaque réservation
- Le client peut le télécharger depuis sa page de confirmation
- Le contrat est pré-rempli avec toutes les infos (loueur, client, véhicule, dates, prix)

## MESSAGERIE
- Conversation directe avec chaque client par réservation
- Le client accède à la messagerie depuis sa page de confirmation
- Tu reçois une notification email quand le client envoie un message

## NOTIFICATIONS
- Configure dans Paramètres → Notifications
- Email : notification par email (activé par défaut)
- Push : notification navigateur (si le client accepte)
- WhatsApp : notification WhatsApp
- Tu reçois des notifications pour : nouvelle réservation, documents reçus, message client

## PARTAGE & FAVORIS
- Les clients peuvent partager tes véhicules via WhatsApp, Facebook, Telegram, ou copier le lien
- Les clients peuvent ajouter tes véhicules en favoris (icône coeur)

## MOT DE PASSE OUBLIÉ
- Sur la page de connexion, clique "Mot de passe oublié ?"
- Entre ton email → tu recevras un lien de réinitialisation par email

## PAIEMENT EN LIGNE (STRIPE CONNECT)
- Va dans Finances → Paiement en ligne
- Clique "Connecter mon compte Stripe" → tu es redirigé vers Stripe
- Stripe te demande : email, téléphone, pièce d'identité, IBAN (compte EUR)
- Un compte Wise, Revolut ou PayPal avec IBAN marche aussi
- Après vérification (quelques minutes à 24h), tes clients peuvent payer en ligne
- L'argent arrive directement sur TON compte bancaire (pas celui de ResaDZ)
- Virements automatiques sous 2-7 jours ouvrés

## COMMENT ÇA MARCHE POUR LES PAIEMENTS
- Le client peut payer par : CB (Visa, Mastercard) ou PayPal
- Paiement de l'acompte : 0% commission ResaDZ, tu reçois 100%
- Paiement total : la commission ResaDZ (8% ou 6%) est prélevée automatiquement
- Frais Stripe : environ 1,5% + 0,25€ par transaction (en plus de la commission)
- Le client peut toujours payer en espèces s'il préfère (comme avant)
- Tu peux voir tes paiements dans ton dashboard Stripe (bouton dans Finances → Paiement en ligne)

## PROBLÈMES FRÉQUENTS
- "Je ne vois pas mon véhicule sur le site" → Vérifie statut = Disponible ET is_active activé
- "Le client ne peut pas réserver" → Vérifie que le statut n'est pas Réservé ou Indisponible
- "Je ne reçois pas les notifications" → Paramètres → Notifications → vérifie que email est activé
- "Mon acompte n'est pas configuré" → Configuration → Acomptes → mets le pourcentage et les méthodes
- "Le visuel ne s'affiche pas" → Vérifie que la marque, le modèle ET la couleur correspondent exactement au template
- "Je ne peux pas créer de véhicule" → Remplis tous les champs obligatoires (marque, modèle, année, transmission, carburant, couleur, places, portes, bagages, prix)
- "Stripe ne marche pas" → Va dans Finances → Paiement en ligne → vérifie que ton compte est connecté et vérifié
- "Le client ne peut pas payer en ligne" → Ton compte Stripe doit être connecté ET vérifié pour que les boutons CB/PayPal apparaissent
KB;
    }

    private function getChauffeurKnowledge(): string
    {
        return <<<'KB'
=== GUIDE COMPLET PANEL CHAUFFEUR RESADZ ===

## STRUCTURE DU MENU
- **Tableau de bord** : Stats courses, revenus, courses en attente
- **Finances → Tableau de bord** : CA transferts vs livraisons (graphique barres 30 jours), dépenses, solde jour/semaine/mois
- **Finances → Transactions** : Historique entrées/sorties, enregistrer des dépenses
- **Transferts** : Réservations de transfert reçues (aéroport, inter-villes)
- **Livraisons** : Réservations de livraison de colis
- **Véhicule chauffeur** : Ton véhicule de service (marque, modèle, année, photo)
- **Options** : Options additionnelles (WiFi, siège bébé, eau, etc.)
- **Configuration → Paramètres** : Profil, services, trajets, notifications

## ONBOARDING CHAUFFEUR
Parcours guidé à la première connexion :
1. CGU & Contrat
2. Profil : Nom, description, expérience, téléphone, WhatsApp
3. Véhicule : Marque, modèle, année, photo du véhicule
4. Services : Cocher les services proposés (transferts, livraisons, courses en ville)
5. Trajets : Ajouter les trajets réguliers avec prix
6. Options : WiFi, siège bébé, eau, etc.
7. Notifications : Email, push, WhatsApp

## COMMISSION
- 10% fixe sur chaque course confirmée (transfert ou livraison)
- Les clients te paient directement en main propre
- ResaDZ envoie une facture hebdomadaire récapitulant les commissions dues
- Tu ne paies rien sans course

## AJOUTER UN TRAJET
- Va dans tes paramètres ou section Trajets
- Ajoute : ville de départ → ville d'arrivée → prix
- Exemple : Alger centre → Aéroport Houari Boumédiène → 3500 DA
- Plus tu ajoutes de trajets, plus tu es visible dans les recherches

## TRANSFERTS
- Un client réserve un transfert → tu reçois un email + notification
- Infos : ville départ, ville arrivée, date, heure, nombre de passagers, prix
- Tu confirmes ou refuses dans ton panel
- Statuts : Pending → Confirmed → Completed

## LIVRAISONS
- Un client commande une livraison de colis → notification
- Infos : ville pickup, ville livraison, description du colis, prix
- Statuts : Pending → Confirmed → Picked up → In transit → Delivered

## FINANCES CHAUFFEUR
- Tableau de bord : graphique barres avec CA transferts (bleu) et CA livraisons (orange) sur 30 jours
- 3 colonnes : Aujourd'hui / Semaine / Mois
- Pour chaque période : CA transferts, CA livraisons, total, dépenses, solde net
- Commission due : 10% sur les courses confirmées/complétées
- Revenus en attente : courses confirmées pas encore terminées
- Tu peux enregistrer tes dépenses (carburant, péages, entretien)

## NOTIFICATIONS
- Email : notification par email pour chaque nouvelle course
- Push : notification navigateur
- WhatsApp : notification WhatsApp
- Configure dans Paramètres → Notifications

## MOT DE PASSE OUBLIÉ
- Page de connexion → "Mot de passe oublié ?" → entre ton email

## PROBLÈMES FRÉQUENTS
- "Je ne reçois pas de courses" → Vérifie que tu as des trajets configurés et que les notifications sont activées
- "Mon CA ne s'affiche pas" → Les revenus s'affichent quand les courses sont marquées "completed" ou "delivered"
- "Comment enregistrer une dépense" → Finances → Transactions → bouton "Créer" → type "Dépense"
KB;
    }

    private function getAdminKnowledge(): string
    {
        return <<<'KB'
=== GUIDE COMPLET PANEL ADMIN RESADZ ===

## STRUCTURE DU MENU
- **Tableau de bord** : KPIs globaux, widgets stats, liens rapides
- **Gestion → Loueurs** : Liste loueurs/chauffeurs, accepter/refuser, voir détails, se connecter en tant que
- **Gestion → Réservations** : Toutes les réservations de la plateforme
- **Gestion → Prospects (CRM)** : Vue Kanban drag-and-drop + import CSV
- **Catalogue → Véhicules** : Tous les véhicules de tous les loueurs
- **Catalogue → Marques** : Gestion des marques (logo, nom, slug)
- **Catalogue → Catégories** : Catégories (Citadine, SUV, Berline, Utilitaire...)
- **Catalogue → Modèles véhicules** : ~200 modèles pré-enregistrés par marque, CRUD
- **Catalogue → Visuels véhicules** : Templates photo (marque + modèle + couleur + image)
- **Catalogue → Options de location** : Options globales disponibles
- **Marketing → Statistiques** : Analytics complet (visites, géographie, trafic, funnel, clics)
- **Marketing → Blog** : Articles SEO avec slug, tags, featured
- **Marketing → Leads/Newsletter** : Abonnés newsletter
- **Configuration → Paramètres** : Settings globaux (logo, nom, WhatsApp, commission, etc.)

## GÉRER LES LOUEURS
- Liste avec filtres : actif/inactif, vérifié, suspendu, en essai, type de compte
- Bouton "Accepter" (vert) : Active le compte + envoie mail d'activation avec lien d'accès
- Bouton "Refuser" (rouge) : Suspend le compte avec raison optionnelle
- Bouton "Accéder au dashboard" : Ouvre le panel loueur en tant que ce loueur
- Bouton "Renvoyer mail bienvenue" : Renvoie le mail de bienvenue
- Onglets dans la fiche : Profil, Statut (actif/vérifié/suspendu), Essai & Commission, Réseaux sociaux

## VISUELS VÉHICULES (TEMPLATES)
- Catalogue → Visuels véhicules → Créer
- Remplis : Marque + Modèle + Couleur + Upload image
- Les loueurs qui créent un véhicule avec le même combo verront ce visuel automatiquement
- Le visuel apparait sur le site public comme photo principale si le loueur n'a pas uploadé sa propre photo
- Le nom du loueur s'affiche en overlay 3D sur l'image

## MODÈLES VÉHICULES
- Catalogue → Modèles véhicules : ~200 modèles pour 29 marques
- Les loueurs peuvent en créer de nouveaux depuis leur formulaire (bouton "Ajouter un modèle")
- Les nouveaux modèles créés par les loueurs sont sauvés en base pour les futurs loueurs
- L'admin peut désactiver/supprimer des modèles obsolètes

## STATISTIQUES
- Marketing → Statistiques (cache fichier 5 min pour la perf)
- Visiteurs en temps réel (badge dans la nav)
- Visites : aujourd'hui, hier, semaine, mois, uniques
- Géographie : top pays, villes, régions avec drapeaux
- Canaux de trafic : direct, recherche, social, référence, email, campagne
- Campagnes UTM
- Heures/jours de la semaine (barres CSS)
- Visites quotidiennes 30 jours (barres CSS)
- Clics sur actions : téléphone, WhatsApp, réservation, voir détails, partage
- Appareils, navigateurs, OS
- Funnel de conversion : Accueil → Liste → Détail → Réservation → Complétée
- Top pages, landing pages, véhicules les plus vus
- Taux de rebond, sessions
- Top IPs avec exclusion possible
- Dernières 30 visites détaillées
- Bouton "Nettoyer anciennes données" (supprimer +90 jours)

## CRM PROSPECTS
- Gestion → Prospects : Vue Kanban avec 5 colonnes
- Colonnes : Non contacté → Contacté → Intéressé → Inscrit → Pas intéressé
- Drag-and-drop HTML5 pour déplacer les cartes
- Import CSV en masse (source, nom, email, téléphone, wilaya, notes)
- Filtres par statut, wilaya, source
- Actions en masse : supprimer, changer statut

## COMMISSION
- Location véhicule : 8% (1-10 jours), 6% (+10 jours)
- Transferts/livraisons : 10% fixe
- Configurable dans Configuration → Paramètres (commission_rate_1_to_10_days, commission_rate_11_plus_days)

## NOTIFICATIONS ADMIN
- Email automatique quand un nouveau loueur s'inscrit (sujet : "Nouveau loueur à valider — ACTION REQUISE")
- Email automatique quand un nouveau véhicule est ajouté
- Email automatique quand une réservation est créée
- Email automatique quand un avis est posté
- Emails envoyés aux adresses configurées : inscription@resadz.com, reservation@resadz.com, avis@resadz.com

## PIXEL FACEBOOK
- Pixel Meta intégré sur toutes les pages publiques
- Événements trackés : PageView, ViewContent (fiche véhicule), InitiateCheckout (page réservation), Purchase (confirmation), Lead (inscription)
- Pixel ID configurable dans Settings (facebook_pixel_id)

## MOT DE PASSE OUBLIÉ
- Page de connexion admin → "Mot de passe oublié ?" → lien vers /mot-de-passe/oublie
KB;
    }
}
