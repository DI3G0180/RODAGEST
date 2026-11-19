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
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'foto' => 'empleados/admin.jpg',
                'fecha_ingreso' => '2019-01-15',
                'puesto' => 'Administrador General',
                'telefono' => '+52 55 1234 5678',
                'unidad_asignada' => null,
            ]
        );

        // 2. Usuario Inspector
        User::updateOrCreate(
            ['email' => 'inspector@rodagest.com'],
            [
                'name' => 'Inspector de Patio',
                'password' => Hash::make('password123'),
                'role' => 'inspector',
                'foto' => 'empleados/inspector.jpg',
                'fecha_ingreso' => '2021-03-10',
                'puesto' => 'Inspector Técnico',
                'telefono' => '+52 55 2345 6789',
                'unidad_asignada' => null,
            ]
        );

        // 3. Usuario Operador / Chofer
        User::updateOrCreate(
            ['email' => 'operador@rodagest.com'],
            [
                'name' => 'Operador / Chófer',
                'password' => Hash::make('password123'),
                'role' => 'operador',
                'foto' => 'empleados/operador.jpg',
                'fecha_ingreso' => '2023-07-01',
                'puesto' => 'Chofer de Flota',
                'telefono' => '+52 55 3456 7890',
                'unidad_asignada' => 'UN-004',
            ]
        );

        // 4. Usuario Limpieza
        User::updateOrCreate(
            ['email' => 'limpieza@rodagest.com'],
            [
                'name' => 'Personal de Limpieza',
                'password' => Hash::make('password123'),
                'role' => 'limpieza',
                'foto' => 'empleados/limpieza.jpg',
                'fecha_ingreso' => '2022-06-20',
                'puesto' => 'Operario de Limpieza',
                'telefono' => '+52 55 4567 8901',
                'unidad_asignada' => 'UN-012',
            ]
        );
    }
}