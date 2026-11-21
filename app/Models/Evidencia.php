<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $fillable = ['inspeccion_id', 'ruta_archivo', 'tipo_media', 'nombre_original'];

    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class);
    }
}
