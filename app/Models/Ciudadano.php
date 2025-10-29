<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciudadano extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // ❌ ELIMINAR tipo_documento y numero_documento
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'numero_celular',
        'direccion_notificacion',
        'fecha_nacimiento',
        'departamento_id',
        'ciudad_id',
        'genero',
        'condicion_especial',
        'foto_perfil',
        'campos_adicionales',
        'perfil_completo',
    ];

    protected $casts = [
        'campos_adicionales' => 'array',
        'fecha_nacimiento' => 'date',
        'perfil_completo' => 'boolean',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function reclamos(): HasMany
    {
        return $this->hasMany(Reclamo::class);
    }

    // ✅ Accesores para obtener datos desde user
    public function getTipoDocumentoAttribute()
    {
        return $this->user->tipo_documento ?? null;
    }

    public function getNumeroDocumentoAttribute()
    {
        return $this->user->numero_documento ?? null;
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->primer_nombre} {$this->segundo_nombre} {$this->primer_apellido} {$this->segundo_apellido}");
    }

    public function getDocumentoCompletoAttribute(): string
    {
        $tipoDoc = $this->user->tipo_documento ?? '';
        $numDoc = $this->user->numero_documento ?? '';
        return trim("{$tipoDoc} {$numDoc}");
    }

    public function verificarPerfilCompleto(): void
    {
        $camposRequeridos = [
            'primer_nombre',
            'primer_apellido',
            'numero_celular',
            'departamento_id',
            'ciudad_id',
            'genero',
        ];

        $completo = true;
        foreach ($camposRequeridos as $campo) {
            if (empty($this->$campo)) {
                $completo = false;
                break;
            }
        }

        // Verificar que el usuario tenga documento
        if (empty($this->user->tipo_documento) || empty($this->user->numero_documento)) {
            $completo = false;
        }

        $this->update(['perfil_completo' => $completo]);
    }
}