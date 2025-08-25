<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = ['nombre', 'estado', 'departamento_id'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function ciudadanos()
    {
        return $this->hasMany(Ciudadano::class, 'ciudad_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 1);
    }
}
