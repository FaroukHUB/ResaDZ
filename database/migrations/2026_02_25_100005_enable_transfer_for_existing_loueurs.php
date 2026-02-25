<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing loueurs already had transfer configured in the old system,
        // so enable the new offers_transfer flag for them
        DB::table('loueurs')->where('account_type', 'loueur')->update([
            'offers_transfer' => true,
        ]);
    }

    public function down(): void
    {
        DB::table('loueurs')->where('account_type', 'loueur')->update([
            'offers_transfer' => false,
        ]);
    }
};
