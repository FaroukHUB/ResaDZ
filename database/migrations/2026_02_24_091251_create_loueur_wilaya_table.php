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
        // Create pivot table
        Schema::create('loueur_wilaya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->onDelete('cascade');
            $table->string('wilaya_code', 2);
            $table->timestamps();

            $table->unique(['loueur_id', 'wilaya_code']);
            $table->index('wilaya_code');
        });

        // Migrate existing data from loueurs.wilaya to pivot table
        $loueurs = DB::table('loueurs')->whereNotNull('wilaya')->where('wilaya', '!=', '')->get();
        $wilayas = config('resadz.wilayas');

        foreach ($loueurs as $loueur) {
            $wilayaCode = null;

            // Try to find the wilaya code from the name
            $searchKey = array_search($loueur->wilaya, $wilayas);
            if ($searchKey !== false) {
                $wilayaCode = $searchKey;
            } else {
                // Try case-insensitive search
                foreach ($wilayas as $code => $name) {
                    if (strtolower($name) === strtolower(trim($loueur->wilaya))) {
                        $wilayaCode = $code;
                        break;
                    }
                }
            }

            if ($wilayaCode !== null) {
                DB::table('loueur_wilaya')->insert([
                    'loueur_id' => $loueur->id,
                    'wilaya_code' => $wilayaCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loueur_wilaya');
    }
};
