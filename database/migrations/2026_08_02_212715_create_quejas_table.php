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
        Schema::create('quejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('operador_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_queja'); // E.g.: "Limpieza", "Mantenimiento", "Daño", etc.
            $table->string('estado')->default('pendiente'); // pendiente, en_proceso, resuelto
            $table->text('descripcion');
            $table->text('comentarios')->nullable();
            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index(['unidad_id', 'estado']);
            $table->index('operador_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quejas');
    }
};