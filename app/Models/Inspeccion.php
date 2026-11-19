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
        'inspector_responsable',
        'tipo_dano',
        'prioridad',
        'estado',
        'descripcion',
        'fecha_reporte',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}