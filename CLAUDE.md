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
- Location véhicule : 1-10j → 8%, +10j → 6% (2 paliers seulement)
- Transferts/livraisons : 10% fixe
- Configurable via Settings (commission_rate_1_to_10_days, commission_rate_11_plus_days)

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

## Ce qui a été fait (session 23 mars 2026)
1. ✅ Fix page 403 : logo dynamique ResaDZ (Setting logo_light) au lieu du logo loueur hardcodé
2. ✅ Numéro WhatsApp dynamique depuis Settings sur la page 403

## TODO - Prochaine session
### Select Modèle dynamique par Marque (véhicules)
- Créer table `vehicle_models` (id, brand_id, name, is_active, timestamps)
- Créer model `VehicleModel` avec relation `belongsTo(Brand)`
- Ajouter relation `hasMany(VehicleModel)` sur `Brand`
- Migration : ajouter `vehicle_model_id` sur `vehicles` (nullable, garder le champ `model` texte en fallback)
- Seeder avec les modèles courants en Algérie :
  - Renault → Clio, Symbol, Duster, Logan, Megane, Kadjar...
  - Hyundai → i10, i20, Tucson, Accent, Creta...
  - Dacia → Sandero, Duster, Logan, Jogger...
  - Peugeot → 208, 308, 2008, 3008, Partner...
  - Volkswagen → Golf, Polo, Tiguan, Caddy...
  - Seat → Ibiza, Leon, Arona, Ateca...
  - Toyota, Kia, Chevrolet, etc.
- Formulaire Filament (Admin + Loueur) :
  - Select Marque (reactive) → Select Modèle (filtré par marque) → full_name auto-rempli
  - Le loueur peut créer un nouveau modèle via `createOptionForm()` si pas dans la liste
  - La finition/version reste dans le champ full_name (texte libre)
- Admin : `VehicleModelResource` pour gérer les modèles (CRUD)
- Ne PAS gérer les finitions (VTR, GTI, etc.) dans la table — c'est dans full_name

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
