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
                ->assertViewHas('userRole', $role)
                ->assertSee('rel="stylesheet"', false)
                ->assertSee('/build/assets/', false)
                ->assertSee('x-teleport="body"', false)
                ->assertSee('rodagest-mobile-panel', false)
                ->assertSee('aria-label="Accesos rápidos"', false)
                ->assertSee('pb-20 md:pb-8', false)
                ->assertSee('hover:bg-teal-50', false);

            $this->post(route('logout', [], false))->assertRedirect('/');
            $this->get(route('login', [], false))->assertOk();
        }
    }

    public function test_dashboard_html_is_not_cached_by_mobile_webviews(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => true,
            'must_change_password' => false,
            'two_factor_enabled' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard', [], false))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->assertHeader('Pragma', 'no-cache')
            ->assertHeader('Expires', '0');
    }

    public function test_units_page_includes_production_vite_stylesheet(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'activo' => true]);

        $this->actingAs($admin)
            ->get(route('unidades.index', [], false))
            ->assertOk()
            ->assertSee('rel="stylesheet"', false)
            ->assertSee('/build/assets/', false);
    }
}
