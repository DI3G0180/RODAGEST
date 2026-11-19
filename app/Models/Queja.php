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
        'operador_id',
        'tipo_queja',
        'estado',
        'descripcion',
        'comentarios',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }
}