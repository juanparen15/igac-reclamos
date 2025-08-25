<?php

namespace App\Filament\Ciudadano\Resources\MisReclamosResource\Pages;

use App\Filament\Ciudadano\Resources\MisReclamosResource;
use App\Filament\Ciudadano\Widgets\AlertaPerfilIncompleto;
use App\Models\Ciudadano;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMisReclamos extends ListRecords
{
    protected static string $resource = MisReclamosResource::class;

    protected function getHeaderActions(): array
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();
        
        if (!$ciudadano || !$ciudadano->perfil_completo) {
            return [];
        }
        
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            AlertaPerfilIncompleto::class,
        ];
    }
}