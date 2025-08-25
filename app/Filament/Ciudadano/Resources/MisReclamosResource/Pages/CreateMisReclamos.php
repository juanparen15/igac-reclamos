<?php

namespace App\Filament\Ciudadano\Resources\MisReclamosResource\Pages;

use App\Filament\Ciudadano\Resources\MisReclamosResource;
use App\Models\Ciudadano;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMisReclamos extends CreateRecord
{
    protected static string $resource = MisReclamosResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();
        $data['ciudadano_id'] = $ciudadano->id;
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}