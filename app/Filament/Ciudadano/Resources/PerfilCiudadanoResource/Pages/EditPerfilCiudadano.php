<?php

namespace App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;

use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EditPerfilCiudadano extends EditRecord
{
    protected static string $resource = PerfilCiudadanoResource::class;
    protected static ?string $title = 'Completar Mi Perfil';

    public function mount($record = null): void
    {
        parent::mount($record);

        // Mostrar notificación al entrar a editar si el perfil está incompleto
        if (!$this->record->perfil_completo) {
            $camposFaltantes = [];
            if (empty($this->record->primer_nombre)) $camposFaltantes[] = 'Primer Nombre';
            if (empty($this->record->primer_apellido)) $camposFaltantes[] = 'Primer Apellido';
            if (empty($this->record->numero_celular)) $camposFaltantes[] = 'Número de Celular';
            if (empty($this->record->genero)) $camposFaltantes[] = 'Género';
            if (empty($this->record->departamento_id)) $camposFaltantes[] = 'Departamento';
            if (empty($this->record->ciudad_id)) $camposFaltantes[] = 'Ciudad';

            if (count($camposFaltantes) > 0) {
                Notification::make()
                    ->warning()
                    ->title('Complete los campos faltantes')
                    ->body('Faltan ' . count($camposFaltantes) . ' campos: ' . implode(', ', $camposFaltantes))
                    ->persistent()
                    ->send();
            }
        }
    }

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
        // Permanecer en la misma página después de guardar
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->body('Los cambios se han guardado correctamente.')
            ->send();
    }

    protected function afterSave(): void
    {
        // Verificar perfil completo
        $camposRequeridos = [
            'primer_nombre',
            'primer_apellido',
            'numero_celular',
            'departamento_id',
            'ciudad_id',
            'genero',
        ];

        $completo = true;
        $camposFaltantes = [];

        foreach ($camposRequeridos as $campo) {
            if (empty($this->record->$campo)) {
                $completo = false;
                
                // Mapear nombres técnicos a nombres legibles
                $nombresCampos = [
                    'primer_nombre' => 'Primer Nombre',
                    'primer_apellido' => 'Primer Apellido',
                    'numero_celular' => 'Número de Celular',
                    'departamento_id' => 'Departamento',
                    'ciudad_id' => 'Ciudad',
                    'genero' => 'Género',
                ];
                
                $camposFaltantes[] = $nombresCampos[$campo] ?? $campo;
            }
        }

        if (empty($this->record->user->tipo_documento) || empty($this->record->user->numero_documento)) {
            $completo = false;
        }

        // Actualizar el estado
        DB::table('ciudadanos')
            ->where('id', $this->record->id)
            ->update(['perfil_completo' => $completo]);

        // Notificaciones según el estado
        if ($completo) {
            // Perfil completo - Notificación de éxito
            Notification::make()
                ->success()
                ->title('¡Perfil Completo!')
                ->body('Su perfil está completo. Ahora puede crear reclamos.')
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('crear_reclamo')
                        ->label('Crear Reclamo')
                        ->button()
                        ->color('success')
                        ->url(route('filament.ciudadano.resources.mis-reclamos.create')),
                    \Filament\Notifications\Actions\Action::make('ir_dashboard')
                        ->label('Ir al Dashboard')
                        ->button()
                        ->url(route('filament.ciudadano.pages.dashboard')),
                ])
                ->send();
        } else {
            // Perfil incompleto - Notificación de advertencia
            Notification::make()
                ->warning()
                ->title('Perfil aún incompleto')
                ->body('Faltan campos por completar: ' . implode(', ', $camposFaltantes))
                ->persistent()
                ->send();
        }

        // Refrescar el registro para actualizar el estado en memoria
        $this->record->refresh();
    }
}