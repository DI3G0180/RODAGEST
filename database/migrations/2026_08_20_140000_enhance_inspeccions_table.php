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
        Schema::table('inspecciones', function (Blueprint $table) {
            // Campo intendente_responsable ya existe de migración anterior, 
            // aseguramos que esté después de inspector_responsable
            if (!Schema::hasColumn('inspecciones', 'intendente_responsable')) {
                $table->foreignId('intendente_responsable')->nullable()->after('inspector_responsable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspecciones', function (Blueprint $table) {
            $table->dropColumn('intendente_responsable');
        });
    }
};