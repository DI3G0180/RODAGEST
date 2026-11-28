<?php

namespace Tests\Feature;

use App\Models\Aseo;
use App\Models\Inspeccion;
use App\Models\Queja;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressiveStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_complaints_cannot_move_back_or_change_after_resolution(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-STATE-Q']);
        $complaint = Queja::create([
            'unidad_id' => $unit->id, 'eco' => $unit->economico, 'area' => 'Patio',
            'tipo_queja' => 'Limpieza', 'estado' => 'en_proceso', 'urgencia' => 'normal',
            'descripcion' => 'Prueba de estado progresivo.',
        ]);

        $this->actingAs($admin)->put(route('quejas.actualizar-estado', [$complaint->id], false), ['estado' => 'pendiente'])
            ->assertStatus(422);
        $this->assertDatabaseHas('quejas', ['id' => $complaint->id, 'estado' => 'en_proceso']);

        $complaint->update(['estado' => 'resuelto']);
        $this->put(route('quejas.actualizar-estado', [$complaint->id], false), ['estado' => 'en_proceso'])
            ->assertStatus(422);
        $this->assertDatabaseHas('quejas', ['id' => $complaint->id, 'estado' => 'resuelto']);
    }

    public function test_inspections_cannot_move_back_or_change_after_resolution(): void
    {
        $inspector = User::factory()->create(['role' => 'inspector', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-STATE-I']);
        $inspection = Inspeccion::create([
            'unidad_id' => $unit->id, 'inspector_id' => $inspector->id,
            'inspector_responsable' => $inspector->name, 'tipo_dano' => 'Mecánica',
            'prioridad' => 'media', 'estado' => 'en_proceso', 'descripcion' => 'Prueba de estado progresivo.',
            'fecha_reporte' => now(),
        ]);

        $this->actingAs($inspector)->post(route('inspecciones.actualizar-estado', [$inspection->id], false), ['estado' => 'pendiente'])
            ->assertStatus(422);
        $this->assertDatabaseHas('inspecciones', ['id' => $inspection->id, 'estado' => 'en_proceso']);

        $inspection->update(['estado' => 'resuelto']);
        $this->post(route('inspecciones.actualizar-estado', [$inspection->id], false), ['estado' => 'en_proceso'])
            ->assertStatus(422);
        $this->assertDatabaseHas('inspecciones', ['id' => $inspection->id, 'estado' => 'resuelto']);
    }

    public function test_cleaning_records_cannot_move_back_or_change_after_completion(): void
    {
        $cleaner = User::factory()->create(['role' => 'limpieza', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-STATE-A']);
        $aseo = Aseo::create([
            'unidad_id' => $unit->id, 'intendente_responsable' => $cleaner->name,
            'area_terminal' => 'Patio', 'fecha_hora' => now(), 'tipo_limpieza' => 'Lavado',
            'estado' => 'en_proceso', 'etapas' => ['barrido' => true, 'pasamanos_asientos' => true, 'cabina_exterior' => true, 'nebulizacion' => true],
        ]);

        $this->actingAs($cleaner)->post(route('aseos.actualizar-estado', [$aseo->id], false), ['estado' => 'pendiente'])
            ->assertStatus(422);
        $this->assertDatabaseHas('aseos', ['id' => $aseo->id, 'estado' => 'en_proceso']);

        $aseo->update(['estado' => 'completado']);
        $this->post(route('aseos.actualizar-estado', [$aseo->id], false), ['estado' => 'en_proceso'])
            ->assertStatus(422);
        $this->assertDatabaseHas('aseos', ['id' => $aseo->id, 'estado' => 'completado']);
    }

    public function test_cleaning_complaint_resolution_requires_the_evidence_action(): void
    {
        $cleaner = User::factory()->create(['role' => 'limpieza', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-STATE-CQ']);
        $complaint = Queja::create([
            'unidad_id' => $unit->id, 'eco' => $unit->economico, 'area' => 'Salón',
            'tipo_queja' => 'Limpieza', 'estado' => 'en_proceso', 'urgencia' => 'normal',
            'descripcion' => 'Se requiere limpieza.',
        ]);

        $this->actingAs($cleaner)->put(route('quejas.actualizar-estado', [$complaint->id], false), ['estado' => 'resuelto'])
            ->assertStatus(422);
        $this->assertDatabaseHas('quejas', ['id' => $complaint->id, 'estado' => 'en_proceso']);
    }

    public function test_profile_renders_visible_password_update_button(): void
    {
        $user = User::factory()->create(['role' => 'operador', 'activo' => true]);

        $this->actingAs($user)->get(route('profile.edit', [], false))
            ->assertOk()
            ->assertSee('Seguridad de la Cuenta')
            ->assertSee('name="current_password"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('bg-[#0E7490]')
            ->assertSee('hover:bg-[#083344]')
            ->assertSee('Guardar Cambios');
    }
}
