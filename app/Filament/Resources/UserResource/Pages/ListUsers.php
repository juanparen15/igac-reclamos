<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Widgets\UsersOverview;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('Nuevo Usuario')
                // ->visible(fn(): bool => Auth::user()->hasRole('super_admin'))
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
                ->modifyQueryUsing(fn(Builder $query) => $query->role('super_admin'))
                ->badge(User::role('super_admin')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-shield-exclamation'),

            // 'ciudadanos' => Tab::make('Ciudadanos')
            //     ->modifyQueryUsing(fn(Builder $query) => $query->role('ciudadano'))
            //     ->badge(User::role('ciudadano')->count())
            //     ->badgeColor('info')
            //     ->icon('heroicon-m-users'),

            'no_verificados' => Tab::make('No Verificados')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(User::whereNull('email_verified_at')->count())
                ->badgeColor('gray')
                ->icon('heroicon-m-x-circle'),
        ];
    }

    // public static function canCreate(): bool
    // {
    //     return Auth::user()->can('crear_usuarios') || Auth::user()->hasRole('admin');
    // }

    // public static function canViewAny(): bool
    // {
    //     return Auth::user()->can('gestionar_usuarios') ||
    //         Auth::user()->can('ver_usuarios') ||
    //         Auth::user()->can('crear_usuarios') ||
    //         Auth::user()->can('editar_usuarios') ||
    //         Auth::user()->can('eliminar_usuarios') ||
    //         Auth::user()->hasRole('admin');
    // }
}
