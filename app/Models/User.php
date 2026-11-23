<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Admin, Inspector, Intendente, Operador
        'puesto',
        'fecha_ingreso',
        'telefono',
        'foto',
        'unidad_asignada',
        'activo',
        'google2fa_secret',
        'two_factor_enabled',
        'must_change_password',
        'motivo_baja',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'activo' => 'boolean',
            'fecha_ingreso' => 'date',
            'google2fa_secret' => 'encrypted',
            'two_factor_enabled' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Determinar si el usuario tiene un rol específico.
     */
    public function esRol($role)
    {
        return $this->role === $role;
    }

    /**
     * Una unidad puede tener un operador asignado.
     */
    public function unidadAsignada()
    {
        return $this->hasOne(Unidad::class, 'operador_id');
    }

    public function unidadesOperadas()
    {
        return $this->hasMany(Unidad::class, 'operador_id');
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class, 'operador_id');
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }
}
