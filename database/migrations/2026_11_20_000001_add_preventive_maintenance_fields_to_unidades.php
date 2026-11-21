<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('unidades')) {
            return;
        }

        Schema::table('unidades', function (Blueprint $table) {
            if (! Schema::hasColumn('unidades', 'proximo_mantenimiento_fecha')) {
                $table->date('proximo_mantenimiento_fecha')->nullable();
            }

            if (! Schema::hasColumn('unidades', 'proximo_mantenimiento_odometro')) {
                $table->unsignedInteger('proximo_mantenimiento_odometro')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('unidades')) {
            return;
        }

        Schema::table('unidades', function (Blueprint $table) {
            if (Schema::hasColumn('unidades', 'proximo_mantenimiento_fecha')) {
                $table->dropColumn('proximo_mantenimiento_fecha');
            }

            if (Schema::hasColumn('unidades', 'proximo_mantenimiento_odometro')) {
                $table->dropColumn('proximo_mantenimiento_odometro');
            }
        });
    }
};
