<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('custom_pickup_location')->nullable()->after('pickup_address');
            $table->string('custom_return_location')->nullable()->after('return_address');
            $table->decimal('custom_delivery_fee', 10, 2)->nullable()->after('delivery_fee');
            $table->decimal('custom_return_fee', 10, 2)->nullable()->after('return_fee');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'custom_pickup_location',
                'custom_return_location',
                'custom_delivery_fee',
                'custom_return_fee',
            ]);
        });
    }
};
