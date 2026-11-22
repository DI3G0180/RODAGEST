<?php

namespace Tests\Feature;

use App\Models\Queja;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_report_requires_and_persists_manual_eco_and_area(): void
    {
        $unit = Unidad::create(['economico' => 'ECO-TEST']);

        $this->post('/reportar', [
            'eco' => $unit->economico,
            'area' => 'Puerta trasera',
            'ruta_reportada' => 'Pantitlán',
            'tipo_queja' => 'Daño o seguridad',
            'descripcion' => 'Revisar cierre.',
        ])->assertRedirect();

        $this->assertDatabaseHas('quejas', [
            'unidad_id' => $unit->id,
            'eco' => 'ECO-TEST',
            'area' => 'Puerta trasera',
            'area_reportada' => 'Puerta trasera',
        ]);
    }

    public function test_authenticated_admin_cannot_open_or_submit_public_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-TEST']);

        $this->actingAs($admin)->get('/reportar')->assertForbidden();
        $this->actingAs($admin)->post('/reportar', [
            'eco' => $unit->economico,
            'area' => 'Puerta trasera',
            'ruta_reportada' => 'Pantitlán',
            'tipo_queja' => 'Daño o seguridad',
            'descripcion' => 'Revisar cierre.',
        ])->assertForbidden();

        $this->assertDatabaseCount('quejas', 0);
    }

    public function test_operator_can_report_only_the_assigned_unit_and_fields_are_persisted(): void
    {
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-TEST', 'operador_id' => $operator->id]);

        $this->actingAs($operator)->post('/quejas', [
            'eco' => 'ECO-TEST',
            'area' => 'Eje delantero',
            'tipo_queja' => 'Falla mecánica',
            'urgencia' => 'alta',
            'descripcion' => 'Se escucha ruido al frenar.',
        ])->assertRedirect(route('quejas.index', absolute: false));

        $this->assertDatabaseHas('quejas', [
            'unidad_id' => $unit->id,
            'operador_id' => $operator->id,
            'eco' => 'ECO-TEST',
            'area' => 'Eje delantero',
            'ruta_reportada' => null,
        ]);
    }
}
