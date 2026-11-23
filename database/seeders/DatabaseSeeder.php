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
        $admin = User::withTrashed()->updateOrCreate(
            ['email' => 'admin@rodagest.com'],
            [
                'name' => 'Administrador RodaGest',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'activo' => true,
                'must_change_password' => false,
            ]
        );
        if ($admin->trashed()) $admin->restore();

        // 2. Usuario Inspector
        $inspector = User::withTrashed()->updateOrCreate(
            ['email' => 'inspector@rodagest.com'],
            [
                'name' => 'Inspector de Patio',
                'password' => Hash::make('password123'),
                'role' => 'inspector',
                'activo' => true,
                'must_change_password' => false,
            ]
        );
        if ($inspector->trashed()) $inspector->restore();

        // 3. Usuario Operador / Chofer
        $operador = User::withTrashed()->updateOrCreate(
            ['email' => 'operador@rodagest.com'],
            [
                'name' => 'Operador / Chofer',
                'password' => Hash::make('password123'),
                'role' => 'operador',
                'activo' => true,
                'must_change_password' => false,
            ]
        );
        if ($operador->trashed()) $operador->restore();

        $operador = User::where('email', 'operador@rodagest.com')->first();
        foreach (['ECO-104', 'ECO-108', 'ECO-112', 'ECO-115'] as $index => $economico) {
            $unidad = Unidad::withTrashed()->updateOrCreate(
                ['economico' => $economico],
                [
                    'tipo_unidad' => 'Autobús',
                    'area' => 'Flota',
                    'estado' => 'operativa',
                    'operador_id' => $index === 0 ? $operador?->id : null,
                ]
            );
            if ($unidad->trashed()) $unidad->restore();
        }

        // 4. Usuario Limpieza
        $limpieza = User::withTrashed()->updateOrCreate(
            ['email' => 'limpieza@rodagest.com'],
            [
                'name' => 'Personal de Limpieza',
                'password' => Hash::make('password123'),
                'role' => 'limpieza',
                'activo' => true,
                'must_change_password' => false,
            ]
        );
        if ($limpieza->trashed()) $limpieza->restore();
    }
}
