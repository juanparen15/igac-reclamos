<?php

namespace App\Filament\Resources\CampoDinamicoResource\Pages;

use App\Filament\Resources\CampoDinamicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCampoDinamicos extends ManageRecords
{
    protected static string $resource = CampoDinamicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
