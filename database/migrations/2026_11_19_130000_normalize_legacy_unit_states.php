<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unidades') || !Schema::hasColumn('unidades', 'estado')) return;

        DB::table('unidades')->where('estado', 'activo')->update(['estado' => 'operativa']);
        DB::table('unidades')->where('estado', 'mantenimiento')->update(['estado' => 'en_taller']);
    }

    public function down(): void
    {
        // Se conservan los estados actuales para no revertir datos operativos válidos.
    }
};
