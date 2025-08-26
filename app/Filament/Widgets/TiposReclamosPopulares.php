<?php

namespace App\Filament\Widgets;

use App\Models\Reclamo;
use App\Models\TipoReclamo;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TiposReclamosPopulares extends ChartWidget
{
    protected static ?string $heading = 'Tipos de Reclamos Más Comunes';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $tipos = TipoReclamo::activo()->get();
        $data = [];
        $labels = [];

        foreach ($tipos as $tipo) {
            $count = Reclamo::whereJsonContains('tipos_reclamo_ids', $tipo->id)->count();
            if ($count > 0) {
                $labels[] = $tipo->nombre;
                $data[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Reclamos',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 205, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->can('ver_estadisticas');
    }
}
