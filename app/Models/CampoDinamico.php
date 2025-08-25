<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoDinamico extends Model
{
    use HasFactory;

    protected $table = 'campos_dinamicos';

    protected $fillable = [
        'seccion',
        'nombre',
        'etiqueta',
        'tipo',
        'opciones',
        'requerido',
        'orden',
        'activo',
    ];

    protected $casts = [
        'opciones' => 'array',
        'requerido' => 'boolean',
        'activo' => 'boolean',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeSeccion($query, $seccion)
    {
        return $query->where('seccion', $seccion);
    }
}