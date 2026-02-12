<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Ajouter la relation loueur
            $table->foreignId('loueur_id')->nullable()->after('id')->constrained()->cascadeOnDelete();

            // Tarification flexible (JSON pour éviter tout hardcode)
            $table->json('pricing')->nullable()->after('price_per_day_eur');
            /*
            Structure pricing:
            {
                "base": {"amount": 15000, "currency": "DZD"},
                "base_eur": {"amount": 60, "currency": "EUR"},
                "by_duration": [
                    {"min_days": 3, "max_days": 6, "discount_percent": 10},
                    {"min_days": 7, "max_days": 13, "discount_percent": 15},
                    {"min_days": 14, "max_days": 29, "discount_percent": 20},
                    {"min_days": 30, "max_days": null, "discount_percent": 25}
                ],
                "by_season": [
                    {"name": "haute_saison", "start": "06-15", "end": "09-15", "percent": 20},
                    {"name": "aid", "start": "04-10", "end": "04-15", "percent": 30}
                ],
                "weekend_surcharge_percent": 0
            }
            */

            // Caution configurable par véhicule
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('pricing');
            $table->string('deposit_currency', 3)->default('DZD')->after('deposit_amount');

            // Options disponibles pour ce véhicule (JSON)
            $table->json('available_options')->nullable()->after('deposit_currency');
            /*
            [
                {"name": "GPS", "price": 500, "currency": "DZD", "per": "day"},
                {"name": "Siège bébé", "price": 300, "currency": "DZD", "per": "day"},
                {"name": "Conducteur additionnel", "price": 1000, "currency": "DZD", "per": "rental"},
                {"name": "WiFi portable", "price": 800, "currency": "DZD", "per": "day"}
            ]
            */

            // Kilométrage
            $table->integer('mileage')->nullable()->after('available_options');
            $table->integer('mileage_limit_per_day')->nullable(); // null = illimité
            $table->decimal('extra_mileage_fee', 10, 2)->nullable(); // Frais par km supplémentaire

            // Spécifications additionnelles
            $table->integer('doors')->nullable();
            $table->string('color')->nullable();
            $table->text('features')->nullable(); // Climatisation, Bluetooth, etc.

            // Disponibilité
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->integer('min_rental_days')->default(1);
            $table->integer('max_rental_days')->nullable(); // null = pas de limite
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['loueur_id']);
            $table->dropColumn([
                'loueur_id',
                'pricing',
                'deposit_amount',
                'deposit_currency',
                'available_options',
                'mileage',
                'mileage_limit_per_day',
                'extra_mileage_fee',
                'doors',
                'color',
                'features',
                'available_from',
                'available_until',
                'min_rental_days',
                'max_rental_days',
            ]);
        });
    }
};
