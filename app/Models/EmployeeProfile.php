<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'curp', 'rfc', 'nss', 'fecha_nacimiento', 'lugar_nacimiento', 'estado_civil', 'contacto_emergencia', 'telefono_emergencia',
        'nombre_padre', 'nombre_madre', 'nombre_conyuge', 'identificacion', 'escolaridad',
        'calle', 'numero', 'colonia', 'municipio', 'estado', 'codigo_postal', 'telefono_alterno',
        'estatus_imss', 'tipo_ingreso', 'fecha_reingreso', 'licencia', 'tarjeton',
    ];

    protected function casts(): array
    {
        return [
            'curp' => 'encrypted', 'rfc' => 'encrypted', 'nss' => 'encrypted',
            'contacto_emergencia' => 'encrypted', 'telefono_emergencia' => 'encrypted',
            'fecha_nacimiento' => 'encrypted', 'lugar_nacimiento' => 'encrypted',
            'estado_civil' => 'encrypted', 'nombre_padre' => 'encrypted',
            'nombre_madre' => 'encrypted', 'nombre_conyuge' => 'encrypted',
            'identificacion' => 'encrypted', 'calle' => 'encrypted', 'numero' => 'encrypted',
            'colonia' => 'encrypted', 'municipio' => 'encrypted', 'estado' => 'encrypted',
            'codigo_postal' => 'encrypted', 'telefono_alterno' => 'encrypted',
            'fecha_reingreso' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
