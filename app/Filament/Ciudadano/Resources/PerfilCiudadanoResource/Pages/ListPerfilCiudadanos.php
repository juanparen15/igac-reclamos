<?php

namespace App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;

use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
use App\Models\Ciudadano;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerfilCiudadanos extends ListRecords
{
    protected static string $resource = PerfilCiudadanoResource::class;

    protected static ?string $title = 'Mi Perfil';

    public function mount(): void
    {
        $ciudadano = auth()->user()->ciudadano;

        if (!$ciudadano) {
            $ciudadano = auth()->user()->ciudadano()->create([
                'primer_nombre' => explode(' ', auth()->user()->name)[0] ?? '',
            ]);
        }

        $this->redirect(PerfilCiudadanoResource::getUrl('edit', ['record' => $ciudadano]));
    }
    // public function mount(): void
    // {
    //     parent::mount();

    //     $user = auth()->user();

    //     // Separar el nombre completo en partes
    //     $nombreParts = preg_split('/\s+/', trim($user->name));

    //     // Inicializar variables
    //     $primerNombre = '';
    //     $segundoNombre = '';
    //     $primerApellido = '';
    //     $segundoApellido = '';

    //     // Asignar según la cantidad de partes encontradas
    //     if (count($nombreParts) === 1) {
    //         $primerNombre = $nombreParts[0];
    //     } elseif (count($nombreParts) === 2) {
    //         $primerNombre = $nombreParts[0];
    //         $primerApellido = $nombreParts[1];
    //     } elseif (count($nombreParts) === 3) {
    //         $primerNombre = $nombreParts[0];
    //         $segundoNombre = $nombreParts[1];
    //         $primerApellido = $nombreParts[2];
    //     } else {
    //         // 4 o más partes → asumimos los dos primeros como nombres y los dos últimos como apellidos
    //         $primerNombre = $nombreParts[0];
    //         $segundoNombre = $nombreParts[1];
    //         $primerApellido = $nombreParts[2];
    //         $segundoApellido = $nombreParts[3];
    //     }

    //     // Si no tiene perfil de ciudadano, lo creamos con los datos iniciales
    //     if (!$user->ciudadano) {
    //         $user->ciudadano()->create([
    //             'primer_nombre'     => $primerNombre,
    //             'segundo_nombre'    => $segundoNombre,
    //             'primer_apellido'   => $primerApellido ?: 'Pendiente',
    //             'segundo_apellido'  => $segundoApellido,
    //             'perfil_completo'   => false,
    //         ]);
    //         $user->refresh();
    //     }

    //     $ciudadano = $user->ciudadano;

    // Obtener o crear el perfil del ciudadano
    // $ciudadano = Ciudadano::firstOrCreate(
    //     ['user_id' => auth()->id()],
    //     [
    //         'primer_nombre' => explode(' ', auth()->user()->name)[0] ?? '',
    //         'segundo_nombre' => explode(' ', auth()->user()->name)[1] ?? '',
    //         'primer_apellido' => explode(' ', auth()->user()->name)[2] ?? '',
    //         'segundo_apellido' => explode(' ', auth()->user()->name)[3] ?? '',
    //     ]
    // );

    // Redirigir automáticamente a la página de edición
    //     $this->redirect(
    //         PerfilCiudadanoResource::getUrl('edit', ['record' => $ciudadano])
    //     );
    // }

    // protected function getHeaderActions(): array
    // {
    //     return [];
    // }

    // public function getBreadcrumbs(): array
    // {
    //     return [];
    // }
}
