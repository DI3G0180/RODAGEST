<?php

namespace Tests\Feature;

use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Unidad;
use App\Models\User;
use App\Notifications\TurnoIniciadoConRequisitosPendientes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OperatorTurnReleaseTest extends TestCase
{
    use RefreshDatabase;

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
            'prioridad' => 'baja', 'estado' => 'reparada', 'descripcion' => 'Inspección conforme',
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
