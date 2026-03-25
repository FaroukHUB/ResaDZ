<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedInteger('fuel_return_fee')->default(0)->after('vehicle_options');
            $table->unsignedInteger('wash_return_fee')->default(0)->after('fuel_return_fee');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['fuel_return_fee', 'wash_return_fee']);
        });
    }
};
