<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loueur_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();

            // Clé-valeur pour flexibilité totale
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, decimal, boolean, json

            $table->unique(['loueur_id', 'key']);
            $table->timestamps();
        });

        // Table pour les paramètres globaux de la plateforme (définis par super admin)
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general'); // general, commission, booking, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loueur_settings');
        Schema::dropIfExists('platform_settings');
    }
};
