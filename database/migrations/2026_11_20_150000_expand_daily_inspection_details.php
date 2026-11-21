<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inspecciones')) {
            Schema::table('inspecciones', function (Blueprint $table) {
                if (! Schema::hasColumn('inspecciones', 'persona_reporta')) $table->string('persona_reporta', 150)->nullable();
                if (! Schema::hasColumn('inspecciones', 'ubicacion_dano')) $table->string('ubicacion_dano', 180)->nullable();
                if (! Schema::hasColumn('inspecciones', 'municipio')) $table->string('municipio', 120)->nullable();
                if (! Schema::hasColumn('inspecciones', 'direccion')) $table->string('direccion', 255)->nullable();
                if (! Schema::hasColumn('inspecciones', 'categoria_evento')) $table->string('categoria_evento', 80)->nullable();
                if (! Schema::hasColumn('inspecciones', 'operador_id')) $table->foreignId('operador_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('evidencias')) {
            Schema::table('evidencias', function (Blueprint $table) {
                if (! Schema::hasColumn('evidencias', 'tipo_media')) $table->string('tipo_media', 12)->default('imagen');
                if (! Schema::hasColumn('evidencias', 'nombre_original')) $table->string('nombre_original')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Se conservan datos de inspección y evidencia al revertir despliegues.
    }
};
