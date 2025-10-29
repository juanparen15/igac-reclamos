<?php

namespace App\Filament\Ciudadano\Resources\MisReclamosResource\Pages;

use App\Filament\Ciudadano\Resources\MisReclamosResource;
use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
use App\Filament\Ciudadano\Widgets\AlertaPerfilIncompleto;
use App\Models\Ciudadano;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListMisReclamos extends ListRecords
{
    protected static string $resource = MisReclamosResource::class;

    public function mount(): void
    {
        parent::mount();

        // Notificar si el perfil está incompleto
        $ciudadano = auth()->user()->ciudadano;
        
        if (!$ciudadano || !$ciudadano->perfil_completo) {
            Notification::make()
                ->warning()
                ->title('⚠️ Perfil Incompleto')
                ->body('Debe completar su perfil antes de crear reclamos.')
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('completar_perfil')
                        ->label('Completar Perfil')
                        ->button()
                        ->color('warning')
                        ->url(PerfilCiudadanoResource::getUrl('edit', ['record' => $ciudadano?->id ?? 1])),
                ])
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        $ciudadano = auth()->user()->ciudadano;
        
        if (!$ciudadano || !$ciudadano->perfil_completo) {
            return [];
        }
        
        return [
            Actions\CreateAction::make()
                ->label('Crear Reclamo')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            AlertaPerfilIncompleto::class,
        ];
    }
}