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

    public function test_admin_can_review_and_update_complaints_but_cannot_create_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-ADMIN-QUEUE']);
        $complaint = Queja::create([
            'unidad_id' => $unit->id,
            'eco' => $unit->economico,
            'area' => 'Puerta trasera',
            'tipo_queja' => 'Daño o seguridad',
            'estado' => 'pendiente',
            'urgencia' => 'alta',
            'descripcion' => 'Revisar seguro de la puerta.',
            'medio_reporte' => 'qr',
        ]);

        $this->actingAs($admin)->get(route('quejas.index', [], false))
            ->assertOk()->assertSee('Ver detalle')->assertSee('Ver formulario público (Vista previa)')->assertDontSee('+ Nuevo reporte');
        $this->get(route('dashboard', [], false))->assertOk()
            ->assertSee(route('quejas.publica.preview', [], false));
        $this->get(route('quejas.publica.preview', [], false))->assertOk()
            ->assertSee('Vista previa administrativa')
            ->assertSee('Envío deshabilitado en vista previa')
            ->assertSee('action="#" method="GET"', false)
            ->assertSee('disabled', false);
        $this->get(route('quejas.qr-publico', [], false))->assertOk()
            ->assertSee('Copiar enlace público')
            ->assertSee('Ver formulario público (Vista previa)');
        $this->get('/reportar')->assertForbidden();
        $this->get(route('quejas.show', [$complaint->id], false))->assertOk()
            ->assertSee('Formulario de reporte')->assertSee('Actualizar seguimiento');
        $this->get(route('quejas.create', [], false))->assertForbidden();
        $this->post(route('quejas.store', [], false), [])->assertForbidden();

        $this->put(route('quejas.actualizar-estado', [$complaint->id], false), ['estado' => 'en_proceso'])
            ->assertRedirect(route('quejas.index', absolute: false));
        $this->assertDatabaseHas('quejas', ['id' => $complaint->id, 'estado' => 'en_proceso']);
    }

    public function test_operator_can_only_open_their_own_complaint_detail(): void
    {
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $otherOperator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-OWN-QUEUE', 'operador_id' => $operator->id]);
        $ownComplaint = Queja::create([
            'unidad_id' => $unit->id, 'eco' => $unit->economico, 'area' => 'Salón',
            'operador_id' => $operator->id, 'tipo_queja' => 'Limpieza', 'estado' => 'pendiente',
            'urgencia' => 'normal', 'descripcion' => 'Solicito limpieza.',
        ]);
        $otherComplaint = Queja::create([
            'unidad_id' => $unit->id, 'eco' => $unit->economico, 'area' => 'Salón',
            'operador_id' => $otherOperator->id, 'tipo_queja' => 'Limpieza', 'estado' => 'pendiente',
            'urgencia' => 'normal', 'descripcion' => 'Reporte de otro operador.',
        ]);

        $this->actingAs($operator)->get(route('quejas.show', [$ownComplaint->id], false))->assertOk();
        $this->get(route('quejas.publica.preview', [], false))->assertForbidden();
        $this->get(route('quejas.show', [$otherComplaint->id], false))->assertNotFound();
    }

    public function test_new_internal_complaint_is_visible_in_the_admin_alert_center_afterward(): void
    {
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-LIVE-COMPLAINT', 'operador_id' => $operator->id]);

        $this->actingAs($operator)->post(route('quejas.store', [], false), [
            'eco' => $unit->economico, 'area' => 'Frenos', 'tipo_queja' => 'Falla mecánica',
            'urgencia' => 'alta', 'descripcion' => 'Vibración al frenar.',
        ])->assertRedirect();

        $this->actingAs($admin)->get(route('dashboard', [], false))
            ->assertOk()->assertSee('1 reportes están abiertos')->assertSee('Centro de alertas');
    }
}
