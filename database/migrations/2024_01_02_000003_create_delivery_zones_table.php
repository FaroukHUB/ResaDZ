<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // Ex: "Aéroport Alger", "Centre-ville Oran"
            $table->string('type')->default('custom'); // airport, train_station, hotel, city_center, custom
            $table->string('city')->nullable();
            $table->string('wilaya')->nullable();

            // Prix configurables par le loueur (AUCUN hardcode)
            $table->decimal('delivery_fee', 10, 2)->nullable(); // Frais de livraison (null = gratuit)
            $table->decimal('return_fee', 10, 2)->nullable(); // Frais de retour si différent
            $table->string('currency', 3)->default('DZD'); // DZD, EUR

            // Options
            $table->boolean('is_active')->default(true);
            $table->boolean('delivery_available')->default(true);
            $table->boolean('return_available')->default(true);

            // Horaires (JSON pour flexibilité)
            $table->json('working_hours')->nullable();
            // Ex: {"mon": {"open": "08:00", "close": "22:00"}, "tue": {...}, "night_fee": 2000}

            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
