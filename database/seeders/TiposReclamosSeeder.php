<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoReclamo;

class TiposReclamosSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            [
                'nombre' => 'Actualización de Linderos',
                'descripcion' => 'Solicitud para actualizar los linderos de un predio',
                'orden' => 1,
            ],
            [
                'nombre' => 'Corrección de Área',
                'descripcion' => 'Solicitud para corregir el área registrada de un predio',
                'orden' => 2,
            ],
            [
                'nombre' => 'Cambio de Propietario',
                'descripcion' => 'Actualización del propietario registrado del predio',
                'orden' => 3,
            ],
            [
                'nombre' => 'División de Predios',
                'descripcion' => 'Solicitud para dividir un predio en múltiples predios',
                'orden' => 4,
            ],
            [
                'nombre' => 'Unificación de Predios',
                'descripcion' => 'Solicitud para unificar varios predios en uno solo',
                'orden' => 5,
            ],
            [
                'nombre' => 'Certificación Catastral',
                'descripcion' => 'Solicitud de certificación catastral del predio',
                'orden' => 6,
            ],
            [
                'nombre' => 'Revisión de Avalúo',
                'descripcion' => 'Solicitud para revisar el avalúo catastral del predio',
                'orden' => 7,
            ],
            [
                'nombre' => 'Otros',
                'descripcion' => 'Otros tipos de reclamos no especificados',
                'orden' => 8,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoReclamo::create($tipo);
        }
    }
}