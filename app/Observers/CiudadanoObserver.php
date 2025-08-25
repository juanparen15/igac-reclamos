<?php

// namespace App\Observers;

// use App\Models\Ciudadano;

// class CiudadanoObserver
// {
//     public function created(Ciudadano $ciudadano)
//     {
//         $ciudadano->verificarPerfilCompleto();
//     }

//     public function updated(Ciudadano $ciudadano)
//     {
//         $ciudadano->verificarPerfilCompleto();
//     }
// }

namespace App\Observers;

use App\Models\Ciudadano;

class CiudadanoObserver
{
    private static $updating = [];

    public function created(Ciudadano $ciudadano)
    {
        $this->verificarPerfilCompleto($ciudadano);
    }

    public function updated(Ciudadano $ciudadano)
    {
        // Evitar loop infinito
        if (isset(self::$updating[$ciudadano->id])) {
            return;
        }

        $this->verificarPerfilCompleto($ciudadano);
    }

    private function verificarPerfilCompleto(Ciudadano $ciudadano)
    {
        $camposRequeridos = [
            'primer_nombre',
            'primer_apellido',
            'numero_celular',
            'direccion_notificacion',
            'fecha_nacimiento',
            'departamento_id',
            'ciudad_id',
            'genero',
        ];

        $completo = true;
        foreach ($camposRequeridos as $campo) {
            if (empty($ciudadano->$campo)) {
                $completo = false;
                break;
            }
        }

        // También verificar que el usuario tenga documento
        if (empty($ciudadano->user->tipo_documento) || empty($ciudadano->user->numero_documento)) {
            $completo = false;
        }

        // Solo actualizar si cambió el estado
        if ($ciudadano->perfil_completo !== $completo) {
            self::$updating[$ciudadano->id] = true;

            $ciudadano->update(['perfil_completo' => $completo]);

            unset(self::$updating[$ciudadano->id]);
        }
    }
}
