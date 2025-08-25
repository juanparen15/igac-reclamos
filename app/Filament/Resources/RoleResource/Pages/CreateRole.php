<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Combinar todos los permisos de las diferentes pestañas
        $allPermissions = collect([
            $data['content_permissions'] ?? [],
            $data['admin_permissions'] ?? [],
            $data['report_permissions'] ?? [],
            $data['special_permissions'] ?? [],
        ])->flatten()->filter()->unique()->values()->toArray();
        
        // Limpiar los campos temporales
        unset($data['content_permissions']);
        unset($data['admin_permissions']);
        unset($data['report_permissions']);
        unset($data['special_permissions']);
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Sincronizar permisos
        $permissions = collect([
            $this->data['content_permissions'] ?? [],
            $this->data['admin_permissions'] ?? [],
            $this->data['report_permissions'] ?? [],
            $this->data['special_permissions'] ?? [],
        ])->flatten()->filter()->unique()->values()->toArray();
        
        if (!empty($permissions)) {
            $this->record->permissions()->sync($permissions);
        }
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rol creado exitosamente';
    }
}