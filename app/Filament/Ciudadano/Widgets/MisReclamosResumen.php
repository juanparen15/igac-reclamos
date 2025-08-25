<?php

namespace App\Filament\Ciudadano\Widgets;

use App\Models\Reclamo;
use App\Models\Ciudadano;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MisReclamosResumen extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

        // Si no hay ciudadano
        if (!$ciudadano) {
            return [
                Stat::make('Sin Perfil', 'No creado')
                    ->description('Cree su perfil para continuar')
                    ->descriptionIcon('heroicon-m-user-plus')
                    ->color('danger')
                    ->url(route('filament.ciudadano.resources.perfil-ciudadanos.index')),
                // ->url(route('filament.ciudadano.pages.mi-perfil')),
            ];
        }

        // Si el perfil está incompleto
        if (!$ciudadano->perfil_completo) {
            return [
                Stat::make('Perfil Incompleto', 'Acción requerida')
                    ->description('Complete su perfil para crear reclamos')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning')
                    // ->url(route('filament.ciudadano.pages.mi-perfil')),
                    ->url(route('filament.ciudadano.resources.perfil-ciudadanos.index')),
                Stat::make('Progreso', $this->calcularProgresoPerfil($ciudadano) . '%')
                    ->description('Completado')
                    ->descriptionIcon('heroicon-m-chart-bar')
                    ->color('info'),
            ];
        }

        // Si el perfil está completo
        $totalReclamos = $ciudadano->reclamos()->count();
        $nuevos = $ciudadano->reclamos()->where('estado', 'nuevo')->count();
        $enProceso = $ciudadano->reclamos()->where('estado', 'en_proceso')->count();
        $resueltos = $ciudadano->reclamos()->whereIn('estado', ['resuelto', 'cerrado'])->count();

        return [
            Stat::make('Total de Reclamos', $totalReclamos)
                ->description($totalReclamos == 0 ? 'Cree su primer reclamo' : 'Todos sus reclamos')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart($totalReclamos > 0 ? $this->getChartData($ciudadano) : null),

            Stat::make('Nuevos', $nuevos)
                ->description('Pendientes')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),

            Stat::make('En Proceso', $enProceso)
                ->description('Siendo atendidos')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Resueltos', $resueltos)
                ->description('Finalizados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    private function calcularProgresoPerfil($ciudadano): int
    {
        $camposRequeridos = [
            'tipo_documento',
            'numero_documento',
            'primer_nombre',
            'primer_apellido',
            'numero_celular',
            'direccion_notificacion',
            'fecha_nacimiento',
            'departamento_id',
            'ciudad_id',
            'genero',
        ];

        $camposCompletos = 0;
        foreach ($camposRequeridos as $campo) {
            if (!empty($ciudadano->$campo)) {
                $camposCompletos++;
            }
        }

        return round(($camposCompletos / count($camposRequeridos)) * 100);
    }

    private function getChartData($ciudadano): array
    {
        $datos = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $count = $ciudadano->reclamos()
                ->whereDate('created_at', $fecha)
                ->count();
            $datos[] = $count;
        }

        return $datos;
    }
}
