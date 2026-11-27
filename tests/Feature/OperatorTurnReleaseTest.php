<?php

namespace Tests\Feature;

use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Unidad;
use App\Models\User;
use App\Notifications\TurnoIniciadoConRequisitosPendientes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OperatorTurnReleaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleaner_can_complete_all_four_stages_without_a_photo(): void
    {
        $cleaner = User::factory()->create(['role' => 'limpieza', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-ASEO-FAST', 'estado' => 'operativa']);

        $this->actingAs($cleaner)->get(route('dashboard', [], false))
            ->assertOk()->assertSee('Aseo rápido de hoy')->assertSee('Pendiente');
        $this->post(route('aseos.completar-rapido', [], false), ['unidad_id' => $unit->id])
            ->assertRedirect()->assertSessionHasNoErrors();

        $aseo = Aseo::where('unidad_id', $unit->id)->firstOrFail();
        $this->assertSame('completado', $aseo->estado);
        $this->assertSame(4, collect($aseo->etapas)->filter()->count());
        $this->assertNull($aseo->evidencia_foto);
        $this->assertDatabaseHas('unidades', ['id' => $unit->id, 'estado' => 'operativa']);

        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $this->actingAs($admin)->get(route('dashboard', [], false))
            ->assertOk()->assertSee('Aseos completados hoy');
    }

    public function test_operator_quick_checklist_registers_all_ok_and_updates_dashboard_indicator(): void
    {
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-CHECK-FAST', 'estado' => 'operativa', 'operador_id' => $operator->id]);

        $this->actingAs($operator)->post(route('turnos.checklist-rapido', [], false), [
            'hay_falla' => '0',
            'checklist' => ['luces' => '1', 'frenos' => '1', 'llantas' => '1', 'fluidos' => '1', 'espejos_seguridad' => '1'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $inspection = Inspeccion::where('unidad_id', $unit->id)->firstOrFail();
        $this->assertSame('resuelto', $inspection->estado);
        $this->assertCount(5, collect($inspection->checklist)->filter());
        $this->get(route('dashboard', [], false))->assertOk()->assertSee('Checklist de hoy registrado.');
    }

    public function test_critical_quick_checklist_marks_unit_out_of_service_and_alerts_admin_queue(): void
    {
        Mail::fake();
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-CHECK-CRITICAL', 'estado' => 'operativa', 'operador_id' => $operator->id]);

        $this->actingAs($operator)->post(route('turnos.checklist-rapido', [], false), [
            'hay_falla' => '1',
            'observaciones' => 'El pedal de freno no responde correctamente.',
            'checklist' => ['luces' => '1', 'frenos' => '0', 'llantas' => '1', 'fluidos' => '1', 'espejos_seguridad' => '1'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('unidades', ['id' => $unit->id, 'estado' => 'fuera_servicio']);
        $this->assertDatabaseHas('inspecciones', ['unidad_id' => $unit->id, 'prioridad' => 'critica', 'dictamen' => 'fuera_servicio']);
        $this->assertDatabaseHas('quejas', ['unidad_id' => $unit->id, 'urgencia' => 'critica', 'estado' => 'pendiente']);

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)->get(route('dashboard', [], false))
            ->assertOk()->assertSee('fallas críticas requieren atención')->assertSee('Centro de alertas');
    }

    public function test_dashboard_recognizes_today_records_by_unit_and_allows_turn_start(): void
    {
        config(['app.timezone' => 'America/Mexico_City']);
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-TEST-1', 'estado' => 'operativa', 'operador_id' => $operator->id]);
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $inspector = User::factory()->create(['role' => 'inspector', 'activo' => true]);

        Inspeccion::create([
            'unidad_id' => $unit->id, 'inspector_id' => $inspector->id,
            'inspector_responsable' => $inspector->name, 'tipo_dano' => 'Sin hallazgo',
            'prioridad' => 'baja', 'estado' => 'pendiente', 'descripcion' => 'Inspección registrada hoy',
            'fecha_reporte' => now(),
        ]);
        Aseo::create([
            'unidad_id' => $unit->id, 'intendente_responsable' => 'Personal de limpieza',
            'area_terminal' => 'Patio', 'fecha_hora' => now(), 'tipo_limpieza' => 'Lavado',
            'estado' => 'completado',
        ]);

        $this->actingAs($operator)->get(route('dashboard', [], false))
            ->assertOk()->assertSee('Inspección de hoy')->assertSee('Aseo completado hoy')
            ->assertSee('UNIDAD LIBERADA');

        Notification::fake();
        $this->post(route('turnos.store', [], false), ['odometro_inicio' => 1200, 'combustible_inicio' => 'lleno'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('turnos', ['unidad_id' => $unit->id, 'operador_id' => $operator->id, 'observaciones' => null]);
        Notification::assertNothingSent();
    }

    public function test_operational_unit_can_start_turn_with_pending_items_and_admin_is_notified(): void
    {
        Notification::fake();
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-TEST-2', 'estado' => 'operativa', 'operador_id' => $operator->id]);
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);

        $this->actingAs($operator)->post(route('turnos.store', [], false), [
            'odometro_inicio' => 50,
            'combustible_inicio' => '1/2',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('turnos', [
            'unidad_id' => $unit->id,
            'operador_id' => $operator->id,
            'observaciones' => 'Turno iniciado con requisitos pendientes: Inspección de hoy, Aseo completado hoy.',
        ]);
        Notification::assertSentTo($admin, TurnoIniciadoConRequisitosPendientes::class);
    }

    public function test_unit_out_of_service_cannot_start_a_shift(): void
    {
        $operator = User::factory()->create(['role' => 'operador', 'activo' => true]);
        Unidad::create(['economico' => 'ECO-TEST-3', 'estado' => 'en_taller', 'operador_id' => $operator->id]);

        $this->actingAs($operator)->post(route('turnos.store', [], false), [
            'odometro_inicio' => 50,
            'combustible_inicio' => '1/2',
        ])->assertStatus(422);
    }
}
