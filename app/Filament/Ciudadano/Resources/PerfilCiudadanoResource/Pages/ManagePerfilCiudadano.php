<?php

namespace App\Filament\Ciudadano\Resources\PerfilCiudadanoResource\Pages;

use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;

class ManagePerfilCiudadano extends ManageRecords
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

    protected function getHeaderActions(): array
    {
        return [];
    }
}