<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chauffeur_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->onDelete('cascade');

            // Informations option
            $table->string('name'); // Nom (WiFi, Boissons, Siege auto, etc.)
            $table->text('description')->nullable(); // Description detaillee
            $table->string('icon')->nullable(); // Icone heroicon ou emoji

            // Tarification
            $table->enum('pricing_type', ['free', 'paid', 'on_request'])->default('free');
            $table->decimal('price', 10, 2)->nullable(); // Prix si payant

            // Categorie
            $table->enum('category', [
                'comfort',      // Confort (WiFi, boissons, journaux)
                'child',        // Enfants (siege auto, rehausseur)
                'accessibility', // Accessibilite (fauteuil roulant)
                'luggage',      // Bagages (valises supplementaires)
                'service',      // Services (accueil aeroport, panneau nom)
                'other'         // Autres
            ])->default('comfort');

            // Disponibilite
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_quantity')->default(1); // Quantite max (ex: 2 sieges auto)

            $table->timestamps();

            // Index
            $table->index(['loueur_id', 'is_active']);
            $table->index(['loueur_id', 'category']);
        });

        // Table pivot pour les options selectionnees sur une reservation transfert
        Schema::create('transfer_booking_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('chauffeur_option_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0); // Prix au moment de la reservation
            $table->timestamps();

            $table->unique(['transfer_booking_id', 'chauffeur_option_id'], 'transfer_option_unique');
        });

        // Table pivot pour les options selectionnees sur une livraison
        Schema::create('delivery_booking_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('chauffeur_option_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['delivery_booking_id', 'chauffeur_option_id'], 'delivery_option_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_booking_options');
        Schema::dropIfExists('transfer_booking_options');
        Schema::dropIfExists('chauffeur_options');
    }
};
