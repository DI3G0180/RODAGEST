<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unidad;
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
                'activo' => true,
            ]
        );

        // 2. Usuario Inspector
        User::updateOrCreate(
            ['email' => 'inspector@rodagest.com'],
            [
                'name' => 'Inspector de Patio',
                'password' => Hash::make('password123'),
                'role' => 'inspector',
                'activo' => true,
            ]
        );

        // 3. Usuario Operador / Chofer
        User::updateOrCreate(
            ['email' => 'operador@rodagest.com'],
            [
                'name' => 'Operador / Chofer',
                'password' => Hash::make('password123'),
                'role' => 'operador',
                'activo' => true,
            ]
        );

        $operador = User::where('email', 'operador@rodagest.com')->first();
        foreach (['ECO-104', 'ECO-108', 'ECO-112', 'ECO-115'] as $index => $economico) {
            Unidad::updateOrCreate(
                ['economico' => $economico],
                [
                    'tipo_unidad' => 'Autobús',
                    'area' => 'Flota',
                    'estado' => 'operativa',
                    'operador_id' => $index === 0 ? $operador?->id : null,
                ]
            );
        }

        // 4. Usuario Limpieza
        User::updateOrCreate(
            ['email' => 'limpieza@rodagest.com'],
            [
                'name' => 'Personal de Limpieza',
                'password' => Hash::make('password123'),
                'role' => 'limpieza',
                'activo' => true,
            ]
        );
    }
}
