<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'deleted_at')) $table->softDeletes();
                if (! Schema::hasColumn('users', 'motivo_baja')) $table->text('motivo_baja')->nullable();
                if (! Schema::hasColumn('users', 'must_change_password')) $table->boolean('must_change_password')->default(false);
            });
        }

        if (Schema::hasTable('unidades')) {
            Schema::table('unidades', function (Blueprint $table): void {
                if (! Schema::hasColumn('unidades', 'deleted_at')) $table->softDeletes();
                if (! Schema::hasColumn('unidades', 'motivo_baja')) $table->text('motivo_baja')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Se preservan columnas de auditoría para no perder estados de baja ni bloquear reversiones.
    }
};
