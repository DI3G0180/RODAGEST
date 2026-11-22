<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('quejas')) {
            return;
        }

        Schema::table('quejas', function (Blueprint $table): void {
            if (! Schema::hasColumn('quejas', 'eco')) {
                $table->string('eco', 30)->nullable();
            }
            if (! Schema::hasColumn('quejas', 'area')) {
                $table->string('area', 80)->nullable();
            }
        });

        if (Schema::hasColumn('quejas', 'telefono_reportante')) {
            Schema::table('quejas', function (Blueprint $table): void {
                $table->dropColumn('telefono_reportante');
            });
        }

        if (Schema::hasColumn('quejas', 'medio_reporte')) {
            DB::table('quejas')->whereNotNull('medio_reporte')
                ->whereNotIn('medio_reporte', ['interno', 'qr', 'app'])
                ->update(['medio_reporte' => 'externo']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('quejas')) {
            return;
        }

        Schema::table('quejas', function (Blueprint $table): void {
            foreach (['eco', 'area'] as $column) {
                if (Schema::hasColumn('quejas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
