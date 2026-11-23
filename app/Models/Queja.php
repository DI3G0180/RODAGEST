<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queja extends Model
{
    use HasFactory;

    protected $table = 'quejas';

    protected $fillable = [
        'unidad_id',
        'eco',
        'area',
        'operador_id',
        'tipo_queja',
        'estado',
        'descripcion',
        'comentarios',
        'urgencia',
        'evidencia_foto',
        'nombre_reportante',
        'medio_reporte',
        'area_reportada',
        'ruta_reportada',
        'codigo_seguimiento',
        'accion_atencion',
        'evidencia_atencion',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class)->withTrashed();
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id')->withTrashed();
    }
}
