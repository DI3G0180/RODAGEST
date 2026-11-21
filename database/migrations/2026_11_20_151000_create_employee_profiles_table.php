<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_profiles')) return;

        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            // These fields hold encrypted values through EmployeeProfile casts; TEXT allows ciphertext expansion.
            foreach (['curp', 'rfc', 'nss', 'fecha_nacimiento', 'lugar_nacimiento', 'estado_civil', 'nombre_padre', 'nombre_madre', 'nombre_conyuge', 'identificacion', 'calle', 'numero', 'colonia', 'municipio', 'estado', 'codigo_postal', 'telefono_alterno'] as $field) {
                $table->longText($field)->nullable();
            }
            $table->string('escolaridad', 120)->nullable();
            $table->string('estatus_imss', 80)->nullable();
            $table->string('tipo_ingreso', 80)->nullable();
            $table->date('fecha_reingreso')->nullable();
            $table->string('licencia', 80)->nullable();
            $table->string('tarjeton', 80)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // La información laboral/personal se conserva para evitar pérdida accidental de expedientes.
    }
};
