<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspeccion extends Model
{
    use HasFactory;

    protected $table = 'inspecciones';

    protected $fillable = [
        'unidad_id',
        'operador_id',
        'inspector_responsable',
        'persona_reporta',
        'ubicacion_dano',
        'municipio',
        'direccion',
        'categoria_evento',
        'tipo_dano',
        'prioridad',
        'estado',
        'descripcion',
        'fecha_reporte',
        'evidencia_foto',
        'checklist',
        'dictamen',
        'inspector_id',
    ];

    protected function casts(): array
    {
        return ['checklist' => 'array', 'fecha_reporte' => 'datetime'];
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }
}
