<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'ciudades';

    protected $fillable = [
        'codigo',
        'nombre',
        'departamento_id',  // ✅ Asegúrate que esté aquí
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function ciudadanos()
    {
        return $this->hasMany(Ciudadano::class, 'ciudad_id');
    }
}