<?php

namespace App\Filament\Resources\TipoReclamoResource\Pages;

use App\Filament\Resources\TipoReclamoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTipoReclamos extends ManageRecords
{
    protected static string $resource = TipoReclamoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
