<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_explains_that_workers_must_contact_the_administrator_for_password_help(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Solicita al Administrador que restablezca tu contraseña.')
            ->assertDontSee('¿Olvidaste tu contraseña?');
    }

    public function test_public_password_recovery_routes_are_not_available(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->post('/forgot-password', ['email' => 'worker@example.test'])->assertNotFound();
        $this->get('/reset-password/example-token')->assertNotFound();
        $this->post('/reset-password', [])->assertNotFound();
    }
}
