<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) $table->string('role')->default('operador');
                if (!Schema::hasColumn('users', 'puesto')) $table->string('puesto')->nullable();
                if (!Schema::hasColumn('users', 'fecha_ingreso')) $table->date('fecha_ingreso')->nullable();
                if (!Schema::hasColumn('users', 'telefono')) $table->string('telefono')->nullable();
                if (!Schema::hasColumn('users', 'foto')) $table->string('foto')->nullable();
            });
        }

        if (Schema::hasTable('unidades')) {
            Schema::table('unidades', function (Blueprint $table) {
                if (!Schema::hasColumn('unidades', 'operador_id')) $table->foreignId('operador_id')->nullable()->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('unidades', 'placas')) $table->string('placas')->nullable();
                if (!Schema::hasColumn('unidades', 'ruta')) $table->string('ruta')->nullable();
                if (!Schema::hasColumn('unidades', 'odometro')) $table->unsignedInteger('odometro')->nullable();
                if (!Schema::hasColumn('unidades', 'estado')) $table->string('estado')->default('activo');
            });
        }

        if (Schema::hasTable('inspecciones')) {
            Schema::table('inspecciones', function (Blueprint $table) {
                if (!Schema::hasColumn('inspecciones', 'evidencia_foto')) $table->string('evidencia_foto')->nullable();
            });
        }

        if (Schema::hasTable('aseos')) {
            Schema::table('aseos', function (Blueprint $table) {
                if (!Schema::hasColumn('aseos', 'tipo_limpieza')) $table->string('tipo_limpieza')->nullable();
                if (!Schema::hasColumn('aseos', 'intendente_responsable')) $table->string('intendente_responsable')->nullable()->change();
                if (!Schema::hasColumn('aseos', 'observaciones')) $table->text('observaciones')->nullable();
                if (!Schema::hasColumn('aseos', 'estado')) $table->string('estado')->default('completado');
            });
        }
    }

    public function down(): void
    {
        // La migración está diseñada para completar instalaciones existentes sin borrar datos.
    }
};
