<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chauffeur_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->onDelete('cascade');

            // Informations vehicule
            $table->string('brand'); // Marque (Toyota, Mercedes, etc.)
            $table->string('model'); // Modele (Corolla, Classe E, etc.)
            $table->year('year')->nullable(); // Annee
            $table->string('license_plate')->nullable(); // Immatriculation
            $table->string('color')->nullable(); // Couleur

            // Type de vehicule
            $table->enum('vehicle_type', ['berline', 'suv', 'van', 'minibus', 'luxury', 'moto'])->default('berline');

            // Capacites
            $table->unsignedTinyInteger('seats')->default(4); // Nombre de places passagers
            $table->unsignedTinyInteger('luggage_capacity')->default(2); // Nombre de valises
            $table->unsignedTinyInteger('hand_luggage_capacity')->default(2); // Bagages a main

            // Caracteristiques
            $table->boolean('has_air_conditioning')->default(true);
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_usb_charger')->default(true);
            $table->boolean('has_child_seat')->default(false);
            $table->boolean('has_wheelchair_access')->default(false);
            $table->boolean('accepts_animals')->default(false);

            // Photos
            $table->json('photos')->nullable(); // Array de chemins photos

            // Statut
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false); // Vehicule principal

            $table->text('notes')->nullable();
            $table->timestamps();

            // Index
            $table->index(['loueur_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chauffeur_vehicles');
    }
};
