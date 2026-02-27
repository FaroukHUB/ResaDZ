<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->timestamp('onboarding_completed_at')->nullable()->after('is_verified');
            $table->unsignedTinyInteger('onboarding_step')->default(0)->after('onboarding_completed_at');
        });

        // Mark existing loueurs as having completed onboarding
        DB::table('loueurs')->whereNotNull('id')->update([
            'onboarding_completed_at' => now(),
            'onboarding_step' => 7, // All steps completed
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed_at', 'onboarding_step']);
        });
    }
};
