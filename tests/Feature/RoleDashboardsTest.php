<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_four_roles_can_sign_in_and_open_their_dashboard(): void
    {
        foreach (['admin', 'inspector', 'operador', 'limpieza'] as $role) {
            $email = "{$role}@rodagest.com";
            User::factory()->create([
                'name' => "Usuario {$role}",
                'email' => $email,
                'password' => 'password123',
                'role' => $role,
                'activo' => true,
                'must_change_password' => false,
                'two_factor_enabled' => false,
            ]);

            $this->post(route('login', [], false), [
                'email' => $email,
                'password' => 'password123',
            ])->assertRedirect(route('dashboard', [], false));

            $this->get(route('dashboard', [], false))
                ->assertOk()
                ->assertViewHas('userRole', $role);

            $this->post(route('logout', [], false))->assertRedirect(route('login', [], false));
            $this->get(route('login', [], false))->assertOk();
        }
    }
}
