<?php

namespace App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;

use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EditPerfilCiudadano extends EditRecord
{
    protected static string $resource = PerfilCiudadanoResource::class;

    protected static ?string $title = 'Completar Mi Perfil';

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return ['Mi Perfil'];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->body('Su perfil ha sido actualizado exitosamente.');
    }

    protected function afterSave(): void
    {
        // Verificar perfil completo sin usar el método del modelo
        $camposRequeridos = [
            'primer_nombre',
            'primer_apellido',
            'numero_celular',
            // 'direccion_notificacion',
            // 'fecha_nacimiento',
            'departamento_id',
            'ciudad_id',
            'genero',
        ];

        $completo = true;
        foreach ($camposRequeridos as $campo) {
            if (empty($this->record->$campo)) {
                $completo = false;
                break;
            }
        }

        if (empty($this->record->user->tipo_documento) || empty($this->record->user->numero_documento)) {
            $completo = false;
        }

        // Actualizar directamente sin disparar observers
        DB::table('ciudadanos')
            ->where('id', $this->record->id)
            ->update(['perfil_completo' => $completo]);

        if ($completo) {
            Notification::make()
                ->success()
                ->title('¡Perfil completo!')
                ->body('Ahora puede crear reclamos en el sistema.')
                ->persistent()
                ->send();
        }
    }

    // protected function afterSave(): void
    // {
    //     // Verificar si el perfil está completo
    //     $this->record->verificarPerfilCompleto();

    //     if ($this->record->perfil_completo && !$this->record->wasRecentlyCreated) {
    //         Notification::make()
    //             ->success()
    //             ->title('¡Perfil completo!')
    //             ->body('Ahora puede crear reclamos en el sistema.')
    //             ->persistent()
    //             ->send();
    //     }
    // }
}




// namespace App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;

// use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
// use App\Models\Ciudadano;
// use Filament\Actions;
// use Filament\Resources\Pages\EditRecord;
// use Filament\Notifications\Notification;
// use Illuminate\Contracts\Support\Htmlable;

// class EditPerfilCiudadano extends EditRecord
// {
//     protected static string $resource = PerfilCiudadanoResource::class;

//     protected static ?string $title = 'Mi Perfil';

//     protected static ?string $navigationLabel = 'Mi Perfil';

//     protected function getHeaderActions(): array
//     {
//         return [];
//     }

//     // public function mount(int|string $record = null): void
//     // {
//     //     $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

//     //     if (!$ciudadano) {
//     //         $this->redirectRoute('filament.ciudadano.auth.login', navigate: true);
//     //         return;
//     //     }

//     //     parent::mount($ciudadano->id);
//     // }

//         public function mount($record = null): void
//     {

//         $nombreParts = explode(' ', auth()->user()->name, 4);

//         // Buscar o crear el perfil del ciudadano
//         $ciudadano = Ciudadano::firstOrCreate(
//         // $ciudadano = \App\Models\Ciudadano::firstOrCreate(
//             ['user_id' => auth()->id()],
//             [
//                 'tipo_documento' => auth()->user()->tipo_documento ?? 'CC',
//                 'numero_documento' => auth()->user()->numero_documento ?? auth()->id(),
//                 'primer_nombre' => $nombreParts[0] ?? '',
//                 'segundo_nombre' => $nombreParts[1] ?? '',
//                 'primer_apellido' => $nombreParts[2] ?? 'Pendiente',
//                 'segundo_apellido' => $nombreParts[3] ?? '',
//                 'numero_celular' => '',
//                 'direccion_notificacion' => '',
//                 'fecha_nacimiento' => now()->subYears(18),
//                 'departamento_id' => null,
//                 'ciudad_id' => null,
//                 'genero' => 'M',
//                 'perfil_completo' => false,
//             ]
//         );

//         parent::mount($ciudadano->id);
//     }

//     protected function getSavedNotification(): ?Notification
//     {
//         $notification = Notification::make()
//             ->success()
//             ->title('Perfil actualizado')
//             ->body('Su perfil ha sido actualizado exitosamente.');

//         // Verificar si el perfil está completo después de guardar
//         $this->record->verificarPerfilCompleto();

//         if ($this->record->perfil_completo) {
//             $notification
//                 ->persistent()
//                 ->body('¡Su perfil está completo! Ya puede crear reclamos.')
//                 ->actions([
//                     \Filament\Notifications\Actions\Action::make('crear_reclamo')
//                         ->label('Crear Reclamo')
//                         ->url(route('filament.ciudadano.resources.mis-reclamos.create'))
//                         ->button(),
//                 ]);
//         }

//         return $notification;
//     }

//     protected function getFormActions(): array
//     {
//         return [
//             $this->getSaveFormAction()
//                 ->label('Guardar Cambios'),
//         ];
//     }

//     protected function getRedirectUrl(): string
//     {
//         return $this->getResource()::getUrl('index');
//     }

//     // Método adicional para personalizar el título
//     public function getTitle(): string | Htmlable
//     {
//         return 'Mi Perfil';
//     }

//     // Método para personalizar el breadcrumb
//     public function getBreadcrumb(): string
//     {
//         return 'Mi Perfil';
//     }
// }
