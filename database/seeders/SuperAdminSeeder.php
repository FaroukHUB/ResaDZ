<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@resadz.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin créé avec succès!');
        $this->command->info('Email: admin@resadz.com');
        $this->command->info('Mot de passe: admin123');
    }
}
