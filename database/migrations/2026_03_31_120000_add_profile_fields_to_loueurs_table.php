<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->json('specialites')->nullable()->after('description');
            $table->json('langues')->nullable()->after('specialites');
            $table->string('horaires')->nullable()->after('langues');
        });
    }

    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn(['specialites', 'langues', 'horaires']);
        });
    }
};
