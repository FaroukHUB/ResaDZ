<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Ajouter la relation loueur (seulement si n'existe pas)
            if (!Schema::hasColumn('vehicles', 'loueur_id')) {
                $table->foreignId('loueur_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            // Tarification flexible (JSON pour éviter tout hardcode)
            if (!Schema::hasColumn('vehicles', 'pricing')) {
                $table->json('pricing')->nullable()->after('price_per_day_eur');
            }

            // Caution configurable par véhicule
            if (!Schema::hasColumn('vehicles', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'deposit_currency')) {
                $table->string('deposit_currency', 3)->default('DZD');
            }

            // Options disponibles pour ce véhicule (JSON)
            if (!Schema::hasColumn('vehicles', 'available_options')) {
                $table->json('available_options')->nullable();
            }

            // Kilométrage
            if (!Schema::hasColumn('vehicles', 'mileage')) {
                $table->integer('mileage')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'mileage_limit_per_day')) {
                $table->integer('mileage_limit_per_day')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'extra_mileage_fee')) {
                $table->decimal('extra_mileage_fee', 10, 2)->nullable();
            }

            // Spécifications additionnelles (doors existe déjà dans create_vehicles)
            if (!Schema::hasColumn('vehicles', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'features')) {
                $table->text('features')->nullable();
            }

            // Disponibilité
            if (!Schema::hasColumn('vehicles', 'available_from')) {
                $table->date('available_from')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'available_until')) {
                $table->date('available_until')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'min_rental_days')) {
                $table->integer('min_rental_days')->default(1);
            }
            if (!Schema::hasColumn('vehicles', 'max_rental_days')) {
                $table->integer('max_rental_days')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Supprimer uniquement les colonnes ajoutées par cette migration
            $columnsToDrop = [
                'loueur_id',
                'pricing',
                'deposit_amount',
                'deposit_currency',
                'available_options',
                'mileage',
                'mileage_limit_per_day',
                'extra_mileage_fee',
                'color',
                'features',
                'available_from',
                'available_until',
                'min_rental_days',
                'max_rental_days',
            ];

            if (Schema::hasColumn('vehicles', 'loueur_id')) {
                $table->dropForeign(['loueur_id']);
            }

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
