<?php

namespace App\Filament\Widgets;

use App\Models\Reclamo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReclamosEstadisticas extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Reclamos', Reclamo::count())
                ->description('Todos los reclamos registrados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
            Stat::make('Reclamos Nuevos', Reclamo::where('estado', 'nuevo')->count())
                ->description('Pendientes de atención')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('En Proceso', Reclamo::where('estado', 'en_proceso')->count())
                ->description('Actualmente en gestión')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Resueltos', Reclamo::where('estado', 'resuelto')->count())
                ->description('Casos resueltos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->can('ver_estadisticas');
    }
}
