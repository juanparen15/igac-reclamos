<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Permiso')
                ->icon('heroicon-m-plus')
                ->visible(fn () => auth()->user()->hasPermissionTo('crear_roles')),
        ];
    }

    public function getTitle(): string 
    {
        return 'Gestión de Permisos';
    }

    public function getSubheading(): ?string
    {
        return 'Administre los permisos individuales del sistema';
    }
}