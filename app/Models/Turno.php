<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $fillable = [
        'operador_id', 'unidad_id', 'odometro_inicial', 'odometro_final',
        'combustible_inicial', 'combustible_final', 'inicio', 'fin', 'estado', 'observaciones',
    ];

    protected function casts(): array
    {
        return ['inicio' => 'datetime', 'fin' => 'datetime'];
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id')->withTrashed();
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class)->withTrashed();
    }
}
