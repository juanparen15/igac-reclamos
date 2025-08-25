<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class UsersOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $activeUsers = User::where('active', true)->count();
        
        return [
            Stat::make('Total Usuarios', $totalUsers)
                ->description('Usuarios registrados en el sistema')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([7, 3, 4, 5, 6, 8, 10])
                ->color('primary'),
                
            Stat::make('Usuarios Verificados', $verifiedUsers)
                ->description(number_format(($verifiedUsers / max($totalUsers, 1)) * 100, 1) . '% del total')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([3, 5, 6, 7, 8, 9, 11])
                ->color('success'),
                
            Stat::make('Usuarios Activos', $activeUsers)
                ->description(($totalUsers - $activeUsers) . ' inactivos')
                ->descriptionIcon('heroicon-m-signal')
                ->chart([5, 4, 6, 8, 7, 9, 10])
                ->color('warning'),
        ];
    }
}