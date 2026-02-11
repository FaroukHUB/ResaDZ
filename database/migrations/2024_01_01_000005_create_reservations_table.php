<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();      // Numéro de réservation unique
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');

            // Client
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone');
            $table->text('client_address')->nullable();

            // Dates et créneaux
            $table->date('start_date');
            $table->time('start_time')->nullable();
            $table->date('end_date');
            $table->time('end_time')->nullable();

            // Lieu
            $table->string('pickup_location')->nullable();  // Lieu de prise en charge
            $table->string('return_location')->nullable();  // Lieu de retour
            $table->string('flight_number')->nullable();    // Numéro de vol (aéroport)

            // Tarification
            $table->decimal('base_price', 10, 2);           // Prix de base
            $table->decimal('options_price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('currency')->default('DZD');

            // Options sélectionnées
            $table->json('selected_options')->nullable();

            // État
            $table->enum('status', [
                'pending',      // En attente de confirmation
                'confirmed',    // Confirmée
                'in_progress',  // En cours (véhicule remis)
                'completed',    // Terminée (véhicule rendu)
                'cancelled'     // Annulée
            ])->default('pending');

            // Notes
            $table->text('client_notes')->nullable();       // Notes du client
            $table->text('admin_notes')->nullable();        // Notes admin (privées)

            // Source
            $table->string('source')->default('website');   // website, phone, admin, whatsapp

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
