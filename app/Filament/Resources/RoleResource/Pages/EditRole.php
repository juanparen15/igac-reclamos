<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => !in_array($this->record->name, ['admin', 'funcionario', 'ciudadano'])),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Combinar todos los permisos
        $allPermissions = collect([
            $data['content_permissions'] ?? [],
            $data['admin_permissions'] ?? [],
            $data['report_permissions'] ?? [],
            $data['special_permissions'] ?? [],
        ])->flatten()->filter()->unique()->values()->toArray();
        
        // Limpiar campos temporales
        unset($data['content_permissions']);
        unset($data['admin_permissions']);
        unset($data['report_permissions']);
        unset($data['special_permissions']);
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Sincronizar permisos
        $permissions = collect([
            $this->data['content_permissions'] ?? [],
            $this->data['admin_permissions'] ?? [],
            $this->data['report_permissions'] ?? [],
            $this->data['special_permissions'] ?? [],
        ])->flatten()->filter()->unique()->values()->toArray();
        
        $this->record->permissions()->sync($permissions);
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Rol actualizado exitosamente';
    }
}