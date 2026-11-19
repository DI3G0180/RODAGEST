<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aseo extends Model
{
    use HasFactory;

    protected $fillable = [
        'unidad_id',
        'intendente_responsable',
        'area_terminal',
        'fecha_hora',
        'comentarios',
    ];

    // Relación con el modelo Unidad (opcional pero muy útil)
    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}