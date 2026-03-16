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
        Schema::table('loueurs', function (Blueprint $table) {
            $table->boolean('is_featured_partner')->default(false)->after('is_verified');
            $table->unsignedInteger('partner_order')->nullable()->after('is_featured_partner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn(['is_featured_partner', 'partner_order']);
        });
    }
};
