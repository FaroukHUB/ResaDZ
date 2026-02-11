<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Ex: "Haute saison été"
            $table->string('slug')->unique();

            // Type de règle
            $table->enum('type', [
                'seasonal',     // Saisonnière (dates)
                'duration',     // Basée sur la durée
                'early_booking' // Réservation anticipée
            ]);

            // Période d'application (pour seasonal)
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Durée (pour duration)
            $table->integer('min_days')->nullable();
            $table->integer('max_days')->nullable();

            // Modification de prix
            $table->enum('modifier_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('modifier_value', 10, 2);   // Ex: -10 pour -10% ou -500 pour -500 DA

            // Priorité (en cas de règles multiples)
            $table->integer('priority')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
