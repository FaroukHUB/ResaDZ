<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('fuel_return_fee', 10, 2)->nullable()->after('available_options');
            $table->decimal('wash_return_fee', 10, 2)->nullable()->after('fuel_return_fee');
            $table->boolean('badge_insurance')->default(false)->after('wash_return_fee');
            $table->boolean('badge_delivery')->default(false)->after('badge_insurance');
            $table->boolean('badge_degressive')->default(false)->after('badge_delivery');
            $table->boolean('badge_airport')->default(false)->after('badge_degressive');
            $table->boolean('badge_km_unlimited')->default(false)->after('badge_airport');
            $table->json('custom_badges')->nullable()->after('badge_km_unlimited');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'fuel_return_fee',
                'wash_return_fee',
                'badge_insurance',
                'badge_delivery',
                'badge_degressive',
                'badge_airport',
                'badge_km_unlimited',
                'custom_badges',
            ]);
        });
    }
};
