<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    use HasFactory;

    protected $table = 'evidencias';
    protected $fillable = ['inspeccion_id', 'ruta_archivo'];

    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class);
    }
}