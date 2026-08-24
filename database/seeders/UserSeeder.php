<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuario Administrador
        User::updateOrCreate(
            ['email' => 'admin@rodagest.com'],
            [
                'name' => 'Administrador RodaGest',
                'password' => Hash::make('password123'), // Cambiar en producción
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

        // 3. Usuario Operador
        User::updateOrCreate(
            ['email' => 'operador@rodagest.com'],
            [
                'name' => 'Operador / Chófer',
                'password' => Hash::make('password123'),
                'role' => 'operador',
            ]
        );
    }
}