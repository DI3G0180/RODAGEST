<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            $table->string('inspector_responsable');
            $table->string('tipo_dano'); // Mecánico, Carrocería, Eléctrico, Neumáticos, Limpieza/Vandalismo, Otro
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto'])->default('pendiente');
            $table->text('descripcion');
            $table->dateTime('fecha_reporte');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};