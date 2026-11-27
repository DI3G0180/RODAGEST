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
                ->assertSee('id="mobile-nav-drawer"', false)
                ->assertSee('window.toggleMobileMenu = function(open)', false)
                ->assertSee('style="display: none; position: fixed; inset: 0; z-index: 99999;"', false)
                ->assertSee('rgba(8, 51, 68, 0.65)', false);

            $this->post(route('logout', [], false))->assertRedirect('/');
            $this->get(route('login', [], false))->assertOk();
        }
    }

    public function test_mobile_drawer_is_a_direct_body_child_and_has_vanilla_fallback(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'activo' => true,
            'must_change_password' => false,
            'two_factor_enabled' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard', [], false))->assertOk();
        $html = $response->getContent();
        $this->assertStringContainsString('window.toggleMobileMenu = function(open)', $html);
        $this->assertStringContainsString('onclick="window.toggleMobileMenu(true)"', $html);
        $this->assertStringContainsString('onclick="window.toggleMobileMenu(false)"', $html);
        $this->assertStringNotContainsString('id="rodagest-splash"', $html, 'El splash se limita al acceso; no se repite dentro de los paneles.');

        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new \DOMXPath($document);
        $drawer = $xpath->query('//body/*[@id="mobile-nav-drawer"]')->item(0);
        $this->assertNotNull($drawer, 'El drawer debe estar directamente dentro de body, fuera de nav y header.');
    }

    public function test_login_shows_splash_and_a_real_csrf_protected_login_form(): void
    {
        $this->get(route('login', [], false))
            ->assertOk()
            ->assertSee('id="rodagest-splash"', false)
            ->assertSee('Control y Gestión Inteligente de Flotas')
            ->assertSee('action="/login"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('Bienvenido de nuevo');
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

    public function test_admin_has_read_only_access_to_inspection_and_cleaning_ledgers(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin', 'activo' => true,
            'must_change_password' => false, 'two_factor_enabled' => false,
        ]);

        $this->actingAs($admin)->get(route('inspecciones.index', [], false))
            ->assertOk()->assertSee('Historial de inspecciones')->assertDontSee('+ Nueva inspección');
        $this->get(route('aseos.index', [], false))
            ->assertOk()->assertSee('Bitácora de aseos')->assertDontSee('Guardar proceso de aseo');
    }
}
