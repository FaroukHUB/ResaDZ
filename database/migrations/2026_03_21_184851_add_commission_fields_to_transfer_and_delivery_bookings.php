<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute les champs de commission pour les chauffeurs (taxis) sur les
     * réservations de transfert et de livraison.
     * Commission fixe de 10% prélevée uniquement au chauffeur.
     */
    public function up(): void
    {
        // Ajout des champs de commission à transfer_bookings
        Schema::table('transfer_bookings', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(10)->after('price');
            $table->decimal('commission_amount', 10, 2)->default(0)->after('commission_rate');
            $table->boolean('commission_paid')->default(false)->after('commission_amount');
            $table->date('commission_paid_at')->nullable()->after('commission_paid');
        });

        // Ajout des champs de commission à delivery_bookings
        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(10)->after('price');
            $table->decimal('commission_amount', 10, 2)->default(0)->after('commission_rate');
            $table->boolean('commission_paid')->default(false)->after('commission_amount');
            $table->date('commission_paid_at')->nullable()->after('commission_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_bookings', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount', 'commission_paid', 'commission_paid_at']);
        });

        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount', 'commission_paid', 'commission_paid_at']);
        });
    }
};
