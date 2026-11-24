<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employee_profiles')) {
            return;
        }

        Schema::table('employee_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_profiles', 'contacto_emergencia')) {
                $table->longText('contacto_emergencia')->nullable();
            }
            if (! Schema::hasColumn('employee_profiles', 'telefono_emergencia')) {
                $table->longText('telefono_emergencia')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Se conservan estos datos para evitar pérdida accidental de información del expediente.
    }
};
