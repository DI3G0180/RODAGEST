<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuario Administrador
        User::updateOrCreate(
            ['email' => 'admin@rodagest.com'],
            [
                'name' => 'Administrador RodaGest',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Usuario Inspector
        User::updateOrCreate(
            ['email' => 'inspector@rodagest.com'],
            [
                'name' => 'Inspector de Patio',
                'password' => Hash::make('password123'),
                'role' => 'inspector',
            ]
        );

        // 3. Usuario Operador / Chofer
        User::updateOrCreate(
            ['email' => 'operador@rodagest.com'],
            [
                'name' => 'Operador / Chofer',
                'password' => Hash::make('password123'),
                'role' => 'operador',
            ]
        );

        // 4. Usuario Limpieza
        User::updateOrCreate(
            ['email' => 'limpieza@rodagest.com'],
            [
                'name' => 'Personal de Limpieza',
                'password' => Hash::make('password123'),
                'role' => 'limpieza',
            ]
        );
    }
}