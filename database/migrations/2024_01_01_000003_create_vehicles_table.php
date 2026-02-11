<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('model');                    // Ex: Tiguan 2025
            $table->string('full_name');                // Ex: VW Tiguan 2025
            $table->string('slug')->unique();           // Ex: vw-tiguan-2025

            // Tarification
            $table->decimal('price_per_day', 10, 2);    // Prix journalier en DA
            $table->decimal('price_per_day_eur', 10, 2)->nullable(); // Prix en EUR
            $table->decimal('price_per_week', 10, 2)->nullable();    // Prix semaine
            $table->decimal('price_per_month', 10, 2)->nullable();   // Prix mois

            // Caractéristiques
            $table->string('transmission')->default('automatic'); // automatic, manual
            $table->string('fuel_type')->default('diesel');       // diesel, essence, hybrid, electric
            $table->integer('seats')->default(5);
            $table->integer('doors')->default(5);
            $table->integer('luggage_capacity')->nullable();       // Nombre de valises
            $table->string('year')->nullable();                    // Année du modèle

            // Images
            $table->string('image');                    // Image principale
            $table->json('gallery')->nullable();        // Galerie d'images

            // État et disponibilité
            $table->enum('status', ['available', 'reserved', 'maintenance', 'unavailable'])
                  ->default('available');
            $table->boolean('is_featured')->default(false);  // Véhicule mis en avant
            $table->boolean('is_active')->default(true);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
