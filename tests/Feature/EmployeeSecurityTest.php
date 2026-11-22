<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeSecurityTest extends TestCase
{
    use RefreshDatabase;

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
    }
}
