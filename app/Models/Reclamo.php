<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reclamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_ticket',
        'ciudadano_id',
        'asunto',
        'mensaje',
        'tipos_reclamo_ids',
        'estado',
        'estado_anterior',
        'archivo_oficio',
        'archivos_adicionales',
        'campos_adicionales',
        'fecha_resolucion',
        'asignado_a',
        'notas_internas',
    ];

    protected $casts = [
        'tipos_reclamo_ids' => 'array',
        'archivos_adicionales' => 'array',
        'campos_adicionales' => 'array',
        'fecha_resolucion' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($reclamo) {
            $reclamo->numero_ticket = 'IGAC-' . date('Y') . '-' . str_pad(Reclamo::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
        });
    }

    public function ciudadano(): BelongsTo
    {
        return $this->belongsTo(Ciudadano::class);
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function getTiposReclamoAttribute()
    {
        return TipoReclamo::whereIn('id', $this->tipos_reclamo_ids ?? [])->get();
    }

    public function getEstadoBadgeColorAttribute(): string
    {
        return match ($this->estado) {
            'nuevo' => 'danger',
            'en_proceso' => 'warning',
            'resuelto' => 'success',
            'cerrado' => 'gray',
            default => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'nuevo' => 'Nuevo',
            'en_proceso' => 'En Proceso',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Cerrado',
            default => $this->estado,
        };
    }
}