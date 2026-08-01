<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';
    protected $fillable = ['economico', 'placa', 'tipo_unidad'];

    public function aseos()
    {
        return $this->hasMany(Aseo::class);
    }

    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class);
    }
}
