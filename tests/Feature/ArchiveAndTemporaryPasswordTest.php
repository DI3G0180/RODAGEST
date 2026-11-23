<?php

namespace Tests\Feature;

use App\Models\Unidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveAndTemporaryPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_temporary_password_must_be_strong_before_user_can_continue(): void
    {
        $employee = User::factory()->create([
            'role' => 'operador',
            'activo' => true,
            'must_change_password' => true,
        ]);

        $this->actingAs($employee)
            ->put(route('password.temporary.update', absolute: false), [
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ])->assertSessionHasErrors('password');

        $this->put(route('password.temporary.update', absolute: false), [
            'password' => 'SecurePass789!',
            'password_confirmation' => 'SecurePass789!',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', ['id' => $employee->id, 'must_change_password' => false]);
    }

    public function test_unit_identity_fields_are_immutable_on_update(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-901', 'modelo' => 'Modelo original', 'area' => 'Patio', 'estado' => 'operativa']);

        $this->actingAs($admin)->put(route('unidades.update', $unit, absolute: false), [
            'economico' => 'ECO-902',
            'modelo' => 'Modelo alterado',
            'area' => 'Terminal',
            'estado' => 'en_taller',
        ])->assertSessionHasErrors(['economico', 'modelo']);

        $this->assertDatabaseHas('unidades', ['id' => $unit->id, 'economico' => 'ECO-901', 'modelo' => 'Modelo original']);
    }

    public function test_unit_with_history_can_be_soft_deleted_and_restored(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $unit = Unidad::create(['economico' => 'ECO-903', 'area' => 'Patio', 'estado' => 'operativa']);

        $this->actingAs($admin)->delete(route('unidades.destroy', $unit, absolute: false), [
            'motivo_baja' => 'Unidad retirada',
        ])->assertRedirect(route('papelera.index', absolute: false));

        $this->assertSoftDeleted('unidades', ['id' => $unit->id]);
        $this->post(route('papelera.unidades.restore', ['id' => $unit->id], absolute: false))
            ->assertRedirect();
        $this->assertDatabaseHas('unidades', ['id' => $unit->id, 'deleted_at' => null, 'motivo_baja' => null]);
    }
}
