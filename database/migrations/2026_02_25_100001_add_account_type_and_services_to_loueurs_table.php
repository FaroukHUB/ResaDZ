<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->string('account_type')->default('loueur')->after('user_id'); // loueur, taxi
            $table->boolean('offers_transfer')->default(false)->after('meta_description');
            $table->boolean('offers_delivery')->default(false)->after('offers_transfer');
        });
    }

    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn(['account_type', 'offers_transfer', 'offers_delivery']);
        });
    }
};
