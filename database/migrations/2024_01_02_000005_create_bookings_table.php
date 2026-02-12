<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Ex: RDZ-2024-ABC123

            // Relations
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();

            // Dates
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('total_days');

            // Livraison (configurable par loueur)
            $table->foreignId('pickup_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->foreignId('return_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->string('pickup_address')->nullable();
            $table->string('return_address')->nullable();
            $table->text('pickup_notes')->nullable(); // Ex: "Vol AH1234, arrivée 14h"
            $table->text('return_notes')->nullable();

            // Prix (tout calculé selon config du loueur, rien de hardcodé)
            $table->decimal('base_price', 10, 2); // Prix de base véhicule
            $table->decimal('duration_discount', 10, 2)->default(0); // Réduction durée
            $table->decimal('season_surcharge', 10, 2)->default(0); // Supplément saison
            $table->decimal('options_total', 10, 2)->default(0); // Total options
            $table->decimal('delivery_fee', 10, 2)->default(0); // Frais livraison
            $table->decimal('return_fee', 10, 2)->default(0); // Frais retour
            $table->decimal('extra_fees', 10, 2)->default(0); // Autres frais
            $table->decimal('discount_amount', 10, 2)->default(0); // Réduction manuelle
            $table->decimal('total_price', 10, 2); // Total final
            $table->string('currency', 3)->default('DZD');

            // Options sélectionnées (JSON)
            $table->json('selected_options')->nullable();

            // Caution
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->string('deposit_currency', 3)->default('DZD');
            $table->enum('deposit_status', ['pending', 'held', 'returned', 'partial', 'kept'])->default('pending');
            $table->text('deposit_notes')->nullable();

            // Acompte (configurable par loueur: montant, délai, etc.)
            $table->decimal('advance_amount', 10, 2)->nullable();
            $table->enum('advance_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('advance_payment_method')->nullable();
            $table->timestamp('advance_paid_at')->nullable();
            $table->timestamp('advance_expires_at')->nullable(); // Timer configurable!

            // Paiement final
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_remaining', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');

            // Infos client (si pas de compte)
            $table->string('client_name')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_whatsapp')->nullable();

            // Documents client
            $table->string('client_id_document')->nullable(); // Photo CNI
            $table->string('client_license_front')->nullable(); // Permis recto
            $table->string('client_license_back')->nullable(); // Permis verso
            $table->string('client_selfie')->nullable();

            // État des lieux
            $table->json('photos_before')->nullable();
            $table->json('photos_after')->nullable();
            $table->text('condition_notes_before')->nullable();
            $table->text('condition_notes_after')->nullable();
            $table->integer('mileage_start')->nullable();
            $table->integer('mileage_end')->nullable();
            $table->string('fuel_level_start')->nullable();
            $table->string('fuel_level_end')->nullable();

            // Contrat
            $table->timestamp('contract_signed_at')->nullable();
            $table->string('contract_signature')->nullable();
            $table->string('contract_pdf')->nullable();

            // Statuts
            $table->enum('status', [
                'pending',      // En attente d'acompte
                'expired',      // Délai acompte dépassé
                'confirmed',    // Acompte reçu
                'active',       // Véhicule pris
                'returning',    // Retour prévu aujourd'hui
                'completed',    // Véhicule rendu
                'cancelled',    // Annulée
                'dispute',      // Litige en cours
            ])->default('pending');

            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by')->nullable(); // client, loueur, admin

            // Avis
            $table->boolean('client_reviewed')->default(false);
            $table->boolean('loueur_reviewed')->default(false);

            // Notes internes
            $table->text('internal_notes')->nullable();

            $table->timestamps();

            // Index pour recherche
            $table->index(['status', 'start_date']);
            $table->index(['loueur_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
