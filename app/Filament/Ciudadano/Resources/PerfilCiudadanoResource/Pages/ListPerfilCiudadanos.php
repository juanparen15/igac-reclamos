<?php

namespace App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;

use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
use App\Models\Ciudadano;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListPerfilCiudadanos extends ListRecords
{
    protected static string $resource = PerfilCiudadanoResource::class;
    protected static ?string $title = 'Mi Perfil';

    public function mount(): void
    {
        $user = auth()->user();
        $ciudadano = $user->ciudadano;

        // Si no existe el perfil, crearlo
        if (!$ciudadano) {
            $nombreParts = preg_split('/\s+/', trim($user->name));
            
            $primerNombre = $nombreParts[0] ?? '';
            $segundoNombre = $nombreParts[1] ?? '';
            $primerApellido = $nombreParts[2] ?? '';
            $segundoApellido = $nombreParts[3] ?? '';

            $ciudadano = $user->ciudadano()->create([
                'primer_nombre' => $primerNombre,
                'segundo_nombre' => $segundoNombre,
                'primer_apellido' => $primerApellido ?: 'Por completar',
                'segundo_apellido' => $segundoApellido,
                'perfil_completo' => false,
            ]);
        }

        // Mostrar notificación si el perfil está incompleto
        if (!$ciudadano->perfil_completo) {
            // Calcular campos faltantes
            $camposFaltantes = [];
            if (empty($ciudadano->primer_nombre)) $camposFaltantes[] = 'Primer Nombre';
            if (empty($ciudadano->primer_apellido)) $camposFaltantes[] = 'Primer Apellido';
            if (empty($ciudadano->numero_celular)) $camposFaltantes[] = 'Número de Celular';
            if (empty($ciudadano->genero)) $camposFaltantes[] = 'Género';
            if (empty($ciudadano->departamento_id)) $camposFaltantes[] = 'Departamento';
            if (empty($ciudadano->ciudad_id)) $camposFaltantes[] = 'Ciudad';

            $mensajeCampos = count($camposFaltantes) > 0 
                ? 'Campos pendientes: ' . implode(', ', $camposFaltantes)
                : 'Complete todos los campos requeridos';

            Notification::make()
                ->warning()
                ->title('Perfil Incompleto')
                ->body($mensajeCampos)
                ->persistent()
                ->send();
        } else {
            // Perfil completo
            Notification::make()
                ->success()
                ->title('Perfil Completo')
                ->body('Su perfil está completo. Puede crear reclamos.')
                ->send();
        }

        // Redirigir a edición
        $this->redirect(
            PerfilCiudadanoResource::getUrl('edit', ['record' => $ciudadano->id])
        );
    }
}