<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unidades';
    protected $fillable = ['economico', 'tipo_unidad', 'modelo', 'area', 'linea', 'operador_id', 'ruta', 'odometro', 'estado', 'proximo_mantenimiento_fecha', 'proximo_mantenimiento_odometro', 'motivo_baja'];

    protected function casts(): array
    {
        return ['proximo_mantenimiento_fecha' => 'date'];
    }

    public function getEtiquetaEconomicoAttribute(): string
    {
        return $this->economico . ($this->trashed() ? ' (Dada de baja)' : '');
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function aseos()
    {
        return $this->hasMany(Aseo::class);
    }

    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class);
    }

    public function quejas()
    {
        return $this->hasMany(Queja::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }
}
