<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quejas')) {
            return;
        }

        Schema::table('quejas', function (Blueprint $table) {
            if (! Schema::hasColumn('quejas', 'nombre_reportante')) $table->string('nombre_reportante', 120)->nullable();
            if (! Schema::hasColumn('quejas', 'telefono_reportante')) $table->string('telefono_reportante', 30)->nullable();
            if (! Schema::hasColumn('quejas', 'medio_reporte')) $table->string('medio_reporte', 20)->default('interno');
            if (! Schema::hasColumn('quejas', 'area_reportada')) $table->string('area_reportada', 80)->nullable();
            if (! Schema::hasColumn('quejas', 'ruta_reportada')) $table->string('ruta_reportada', 120)->nullable();
            if (! Schema::hasColumn('quejas', 'codigo_seguimiento')) $table->string('codigo_seguimiento', 32)->nullable()->unique();
            if (! Schema::hasColumn('quejas', 'accion_atencion')) $table->text('accion_atencion')->nullable();
            if (! Schema::hasColumn('quejas', 'evidencia_atencion')) $table->string('evidencia_atencion')->nullable();
        });

        if (Schema::hasColumn('quejas', 'operador_id')) {
            Schema::table('quejas', function (Blueprint $table) {
                $table->dropForeign(['operador_id']);
            });

            Schema::table('quejas', function (Blueprint $table) {
                $table->unsignedBigInteger('operador_id')->nullable()->change();
                $table->foreign('operador_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Se conservan campos de seguimiento y la relación nullable para no perder reportes públicos.
    }
};
