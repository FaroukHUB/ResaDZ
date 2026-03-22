# ResaDZ - Contexte Projet

## Description
ResaDZ est une marketplace de location de véhicules entre particuliers en Algérie (58 wilayas).
Laravel 11 + Filament (admin + loueur panels). Hébergé sur O2Switch.

## Architecture
- **Frontend** : Blade + Vanilla JS + Tailwind CSS
- **Backend** : Laravel 11, PHP 8.2+
- **Admin Panel** : Filament (app/Filament/Admin/)
- **Loueur Panel** : Filament (app/Filament/Loueur/) — aussi utilisé par les chauffeurs (account_type=taxi)
- **Base de données** : MySQL
- **Hébergement** : O2Switch (mutualisé) — domaine : resadz.mon-agenceweb.fr → resadz.com

## Types de comptes
- **Loueur** (account_type=loueur) : publie des véhicules à louer
- **Chauffeur/Taxi** (account_type=taxi) : propose des transferts et livraisons
- **Client** : réserve des véhicules, commande des transferts

## Modèles principaux
- `Vehicle` : véhicules à louer (prix, dispo, specs, slug → /vehicule/{slug})
- `Loueur` : loueurs et chauffeurs (wilayas, rating, services)
- `Booking` : réservations de véhicules
- `TransferBooking` : réservations de transferts
- `TransferRoute` : trajets disponibles (départ → destination, prix)
- `ChauffeurVehicle` : véhicules des chauffeurs
- `ChauffeurOption` : options additionnelles (WiFi, siège bébé, etc.)
- `DeliveryBooking` : livraisons de colis
- `Review` : avis clients (rating_overall, rating_vehicle, etc.)
- `VehicleOffer` : promotions (% ou montant fixe)
- `VehicleBoost` : mise en avant payante
- `Brand`, `Category` : taxonomie véhicules
- `Setting` : config globale du site

## Commission
- Location véhicule : 1-3j → 8%, 4-7j → 6%, 8j+ → 5%
- Transferts/livraisons : 10% fixe
- Configurable via Settings (commission_rate_1_to_3_days, etc.)

## Chatbot (Résabot)
- **API** : Groq (gratuit, sans CB) — modèle llama-3.3-70b-versatile
- **Clé** : GROQ_API_KEY dans .env sur O2Switch
- **Controller** : app/Http/Controllers/Api/ChatbotController.php
- **Knowledge base** : injecte automatiquement dans le prompt toutes les données réelles :
  - Catalogue véhicules (prix, specs, liens directs)
  - Chauffeurs et trajets transfert
  - Loueurs actifs avec notes/avis
  - Promotions en cours
  - Derniers avis clients
- **Cache auto-refresh** : ChatbotCacheObserver vide le cache dès qu'un Vehicle, Loueur, TransferRoute, ChauffeurVehicle, VehicleOffer ou Review change
- **Micro vocal** : Web Speech Recognition (fr-FR), bouton entre input et send
- **Frontend** : public/js/chatbot.js, public/css/chatbot.css, resources/views/partials/chatbot.blade.php

## Ce qui a été fait (session 22 mars 2026)
1. ✅ Migration chatbot de OpenRouter → Groq (gratuit, sans CB)
2. ✅ Chatbot intelligent avec données réelles de la BDD (véhicules, prix, chauffeurs, avis, promos)
3. ✅ Auto-refresh du cache via Observers (ChatbotCacheObserver)
4. ✅ Compréhension darija/franco-arabe dans le prompt (chhal, bch7al, kayen, etc.)
5. ✅ Liens directs vers les véhicules (/vehicule/slug)
6. ✅ Bouton micro (reconnaissance vocale) ajouté au chatbot
7. ✅ Fix double envoi audio
8. ✅ Fix détection chauffeurs/taxis dans la knowledge base

## TODO - Prochaine session
### Amélioration UX Panel Chauffeur
- Revoir l'interface du dashboard chauffeur (Filament Loueur panel, account_type=taxi)
- Vérifier les pages : transferts, véhicules chauffeur, options, réservations
- Améliorer la navigation et l'ergonomie
- S'assurer que le onboarding chauffeur est fluide

### Amélioration UX Panel Admin
- Revoir l'interface admin (Filament Admin panel)
- Vérifier la gestion des loueurs, véhicules, réservations, commissions
- Dashboard admin : stats, KPIs, alertes
- Modération des avis, gestion des litiges

## Commandes utiles (serveur O2Switch)
```bash
git pull origin claude/explore-resadz-structure-gga4n
php artisan config:cache
php artisan cache:clear
```

## Branche de dev
`claude/explore-resadz-structure-gga4n`
