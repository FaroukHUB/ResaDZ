<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make starts_at and ends_at nullable for pending boosts
        Schema::table('vehicle_boosts', function (Blueprint $table) {
            $table->timestamp('starts_at')->nullable()->change();
            $table->timestamp('ends_at')->nullable()->change();
        });

        // Update status enum to include 'pending_payment'
        DB::statement("ALTER TABLE vehicle_boosts MODIFY COLUMN status ENUM('pending', 'pending_payment', 'active', 'expired', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert status enum
        DB::statement("ALTER TABLE vehicle_boosts MODIFY COLUMN status ENUM('pending', 'active', 'expired', 'cancelled') DEFAULT 'pending'");

        // Note: We don't revert nullable changes as it could cause data loss
    }
};
