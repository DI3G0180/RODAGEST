<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Campo de fotografía de perfil (ruta a storage/app/public/empleados/)
            if (!Schema::hasColumn('users', 'foto')) {
                $table->string('foto')->nullable()->after('password');
            }
            // Fecha de ingreso a la empresa para cálculo de antigüedad
            if (!Schema::hasColumn('users', 'fecha_ingreso')) {
                $table->date('fecha_ingreso')->nullable()->after('foto');
            }
            // Puesto/cargo del empleado
            if (!Schema::hasColumn('users', 'puesto')) {
                $table->string('puesto')->nullable()->after('fecha_ingreso');
            }
            // Teléfono de emergencia o contacto
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono')->nullable()->after('puesto');
            }
            // Unidad asignada (solo para operadores/conductores)
            if (!Schema::hasColumn('users', 'unidad_asignada')) {
                $table->string('unidad_asignada')->nullable()->after('telefono');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['unidad_asignada', 'telefono', 'puesto', 'fecha_ingreso', 'foto']);
        });
    }
};
