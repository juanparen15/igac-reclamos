<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = ['nombre'];

    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'departamento_id');
    }

    public function ciudadanos()
    {
        return $this->hasMany(Ciudadano::class, 'departamento_id');
    }
}
