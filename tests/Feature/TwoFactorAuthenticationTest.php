<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_two_factor_must_verify_totp_before_dashboard_access(): void
    {
        $google2fa = app(Google2FA::class);
        $secret = $google2fa->generateSecretKey();
        $user = User::factory()->create([
            'role' => 'inspector',
            'activo' => true,
            'google2fa_secret' => $secret,
            'two_factor_enabled' => true,
        ]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('2fa.challenge', absolute: false));
        $this->assertGuest();
        $this->get('/2fa/verify')->assertOk();

        $this->post('/2fa/verify', ['code' => $google2fa->getCurrentOtp($secret)])
            ->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }
}
