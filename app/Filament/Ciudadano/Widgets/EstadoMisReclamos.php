<?php

namespace App\Filament\Ciudadano\Widgets;

use App\Models\Reclamo;
use App\Models\Ciudadano;
use Filament\Widgets\ChartWidget;

class EstadoMisReclamos extends ChartWidget
{
    protected static ?string $heading = 'Estado de Mis Reclamos';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 1,
        'lg' => 1,
    ];

    protected function getData(): array
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

        // Si no hay ciudadano o perfil incompleto
        if (!$ciudadano || !$ciudadano->perfil_completo) {
            return [
                'datasets' => [
                    [
                        'label' => 'Sin datos',
                        'data' => [100],
                        'backgroundColor' => ['rgba(229, 231, 235, 0.5)'],
                    ],
                ],
                'labels' => ['Complete su perfil'],
            ];
        }

        $estados = [
            'nuevo' => ['label' => 'Nuevos', 'color' => 'rgba(59, 130, 246, 0.8)'],
            'en_proceso' => ['label' => 'En Proceso', 'color' => 'rgba(245, 158, 11, 0.8)'],
            'resuelto' => ['label' => 'Resueltos', 'color' => 'rgba(34, 197, 94, 0.8)'],
            'cerrado' => ['label' => 'Cerrados', 'color' => 'rgba(107, 114, 128, 0.8)'],
        ];

        $data = [];
        $labels = [];
        $backgroundColor = [];
        $totalReclamos = 0;

        foreach ($estados as $key => $config) {
            $count = $ciudadano->reclamos()->where('estado', $key)->count();
            if ($count > 0) {
                $labels[] = $config['label'] . ' (' . $count . ')';
                $data[] = $count;
                $backgroundColor[] = $config['color'];
                $totalReclamos += $count;
            }
        }

        // Si no hay reclamos
        if (empty($data)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Sin reclamos',
                        'data' => [100],
                        'backgroundColor' => ['rgba(156, 163, 175, 0.3)'],
                    ],
                ],
                'labels' => ['Sin reclamos aún'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Reclamos',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "
                            function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                label += percentage + '%';
                                return label;
                            }
                        ",
                    ],
                ],
            ],
            'maintainAspectRatio' => true,
            'cutout' => '60%',
        ];
    }

    public static function canView(): bool
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();
        return $ciudadano !== null;
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->can('ver_estadisticas');
    }
}
