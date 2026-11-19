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
       Schema::create('aseos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
        $table->string('intendente_responsable');
        $table->string('area_terminal'); // Plataforma / Área
        $table->dateTime('fecha_hora');
        $table->text('comentarios')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aseos');
    }
};
