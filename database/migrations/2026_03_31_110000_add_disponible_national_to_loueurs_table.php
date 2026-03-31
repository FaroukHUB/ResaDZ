<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->boolean('disponible_national')->default(false)->after('offers_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn('disponible_national');
        });
    }
};
