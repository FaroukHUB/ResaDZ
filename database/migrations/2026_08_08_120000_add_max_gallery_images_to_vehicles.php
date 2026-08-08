<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vehicles', 'max_gallery_images')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedTinyInteger('max_gallery_images')->default(5)->after('gallery');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vehicles', 'max_gallery_images')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('max_gallery_images');
            });
        }
    }
};
