<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_directory_and_individual_tabs_render_without_legacy_bulk_edit_section(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'inspector', 'name' => 'Inspectora QA']);

        $this->actingAs($admin)
            ->get(route('empleados.index', [], false))
            ->assertOk()
            ->assertSee('Ver expediente y gestionar')
            ->assertDontSee('Editar accesos y gestionar cuentas de esta página');

        $this->get(route('empleados.show', $worker, false))
            ->assertOk()
            ->assertSee("x-data=\"{ tab: 'expediente' }\"", false)
            ->assertSee('Expediente / hoja de vida')
            ->assertSee('Editar accesos y cuenta')
            ->assertSee('Dar de baja trabajador');
    }

    public function test_employee_start_date_must_be_in_2026_or_later_and_is_persisted(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $this->actingAs($admin);
        $payload = [
            'name' => 'Personal con alta nueva',
            'email' => 'alta.nueva@example.test',
            'role' => 'inspector',
            'password' => 'StrongPass123!',
            'fecha_ingreso' => '2025-12-31',
        ];

        $this->post('/empleados', $payload)->assertSessionHasErrors('fecha_ingreso');
        $this->assertDatabaseMissing('users', ['email' => $payload['email']]);

        $payload['fecha_ingreso'] = '2026-01-01';
        $this->post('/empleados', $payload)->assertSessionHasNoErrors();
        $empleado = User::where('email', $payload['email'])->firstOrFail();
        $this->assertSame('2026-01-01', $empleado->fecha_ingreso->toDateString());
    }

    public function test_admin_cannot_create_a_second_admin_and_employee_password_must_be_strong(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $this->actingAs($admin);

        $payload = [
            'name' => 'Nuevo personal',
            'email' => 'nuevo.personal@example.test',
            'role' => 'inspector',
            'password' => 'weakpass',
        ];

        $this->post('/empleados', $payload)->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => $payload['email']]);

        $payload['password'] = 'StrongPass123!';
        $payload['role'] = 'admin';
        $this->post('/empleados', $payload)->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => $payload['email']]);

        $payload['role'] = 'inspector';
        $this->post('/empleados', $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => $payload['email'], 'role' => 'inspector']);
        $this->assertTrue(Hash::check('StrongPass123!', User::where('email', $payload['email'])->firstOrFail()->password));
        $this->assertDatabaseHas('users', ['email' => $payload['email'], 'must_change_password' => true]);
        $this->post('/logout');
        $this->post('/login', ['email' => $payload['email'], 'password' => 'StrongPass123!'])->assertRedirect(route('password.temporary.edit', absolute: false));
        $this->put(route('password.temporary.update', absolute: false), ['password' => 'PersonalPass456!', 'password_confirmation' => 'PersonalPass456!'])
            ->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', ['email' => $payload['email'], 'must_change_password' => false]);
    }

    public function test_admin_can_send_workers_to_trash_but_not_admin_or_the_current_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);
        $worker = User::factory()->create(['role' => 'inspector', 'activo' => true]);
        $this->actingAs($admin);

        $this->delete(route('empleados.destroy', $worker, absolute: false), ['motivo_baja' => 'Fin de relación laboral'])
            ->assertRedirect(route('papelera.index', absolute: false));
        $this->assertSoftDeleted('users', ['id' => $worker->id]);
        $this->assertDatabaseHas('users', ['id' => $worker->id, 'activo' => false, 'motivo_baja' => 'Fin de relación laboral']);

        $this->delete(route('empleados.destroy', $admin, absolute: false))->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
