<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Widgets\UsersOverview;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Usuario')
                ->icon('heroicon-m-user-plus'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            UsersOverview::class,
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-user-group'),
                
            'admins' => Tab::make('Administradores')
                ->modifyQueryUsing(fn (Builder $query) => $query->role('admin'))
                ->badge(User::role('admin')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-shield-exclamation'),
                
            'funcionarios' => Tab::make('Funcionarios')
                ->modifyQueryUsing(fn (Builder $query) => $query->role('funcionario'))
                ->badge(User::role('funcionario')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-briefcase'),
                
            'ciudadanos' => Tab::make('Ciudadanos')
                ->modifyQueryUsing(fn (Builder $query) => $query->role('ciudadano'))
                ->badge(User::role('ciudadano')->count())
                ->badgeColor('info')
                ->icon('heroicon-m-users'),
                
            'no_verificados' => Tab::make('No Verificados')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(User::whereNull('email_verified_at')->count())
                ->badgeColor('gray')
                ->icon('heroicon-m-x-circle'),
        ];
    }
}