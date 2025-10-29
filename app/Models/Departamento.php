<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',      // ✅ Agregado según tu migración
        'nombre'
    ];

    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'departamento_id');
    }

    public function ciudadanos()
    {
        return $this->hasMany(Ciudadano::class, 'departamento_id');
    }
}