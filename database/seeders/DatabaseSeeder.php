<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer l'utilisateur admin
        User::firstOrCreate(
            ['email' => 'admin@resadz.com'],
            [
                'name' => 'Admin ResaDZ',
                'password' => Hash::make('admin123'),
            ]
        );

        // Importer les données
        $this->call([
            DefaultDataSeeder::class,
            VehicleSeeder::class,
        ]);
    }
}
