<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_step')->default(0)->after('onboarding_step');
        });
    }

    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn('reminder_step');
        });
    }
};
