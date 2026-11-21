<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'activo')) $table->boolean('activo')->default(true);
            });
        }

        if (Schema::hasTable('unidades')) {
            Schema::table('unidades', function (Blueprint $table) {
                if (!Schema::hasColumn('unidades', 'modelo')) $table->string('modelo')->nullable();
                if (!Schema::hasColumn('unidades', 'linea')) $table->string('linea')->nullable();
            });
        }

        if (Schema::hasTable('inspecciones')) {
            Schema::table('inspecciones', function (Blueprint $table) {
                if (!Schema::hasColumn('inspecciones', 'checklist')) $table->json('checklist')->nullable();
                if (!Schema::hasColumn('inspecciones', 'dictamen')) $table->string('dictamen')->default('operativa');
                if (!Schema::hasColumn('inspecciones', 'inspector_id')) $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
                if (Schema::hasColumn('inspecciones', 'estado')) $table->string('estado')->default('pendiente')->change();
            });
            DB::table('inspecciones')->where('estado', 'en_proceso')->update(['estado' => 'pendiente']);
            DB::table('inspecciones')->where('estado', 'resuelto')->update(['estado' => 'reparada']);
        }

        if (Schema::hasTable('quejas')) {
            Schema::table('quejas', function (Blueprint $table) {
                if (!Schema::hasColumn('quejas', 'urgencia')) $table->string('urgencia')->default('normal');
                if (!Schema::hasColumn('quejas', 'evidencia_foto')) $table->string('evidencia_foto')->nullable();
            });
        }

        if (Schema::hasTable('aseos')) {
            Schema::table('aseos', function (Blueprint $table) {
                if (!Schema::hasColumn('aseos', 'etapas')) $table->json('etapas')->nullable();
                if (!Schema::hasColumn('aseos', 'producto_desinfectante')) $table->string('producto_desinfectante')->nullable();
                if (!Schema::hasColumn('aseos', 'evidencia_foto')) $table->string('evidencia_foto')->nullable();
            });
        }

        if (Schema::hasTable('users') && Schema::hasTable('unidades') && !Schema::hasTable('turnos')) {
            Schema::create('turnos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('operador_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('unidad_id')->constrained('unidades')->cascadeOnDelete();
                $table->unsignedInteger('odometro_inicial');
                $table->unsignedInteger('odometro_final')->nullable();
                $table->string('combustible_inicial', 10);
                $table->string('combustible_final', 10)->nullable();
                $table->dateTime('inicio');
                $table->dateTime('fin')->nullable();
                $table->string('estado')->default('abierto');
                $table->timestamps();
                $table->index(['operador_id', 'estado']);
                $table->index(['unidad_id', 'inicio']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('turnos')) Schema::drop('turnos');
        // La migración completa instalaciones ya existentes; no elimina columnas con datos.
    }
};
