<?php

namespace App\Filament\Ciudadano\Resources\MisReclamosResource\Pages;

use App\Filament\Ciudadano\Resources\MisReclamosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisReclamos extends EditRecord
{
    protected static string $resource = MisReclamosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
