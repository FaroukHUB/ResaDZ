<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add EUR equivalents for display (values set by loueur, not converted)
            $table->decimal('total_price_eur', 10, 2)->nullable()->after('total_price')
                ->comment('Total en euros (si le véhicule a un prix en euros)');
            $table->decimal('deposit_amount_eur', 10, 2)->nullable()->after('deposit_currency')
                ->comment('Caution en euros (définie par le loueur)');
            $table->decimal('advance_amount_eur', 10, 2)->nullable()->after('advance_amount')
                ->comment('Acompte en euros (calculé à partir du pourcentage)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['total_price_eur', 'deposit_amount_eur', 'advance_amount_eur']);
        });
    }
};
