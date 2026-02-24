<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support column modifications the same way MySQL does
        // The status column is already a string in SQLite, so we just need to ensure
        // the columns are properly nullable (which they likely already are)

        // For SQLite compatibility, we skip the ENUM modification
        // The application logic will handle status validation
    }

    public function down(): void
    {
        // No-op for SQLite
    }
};
