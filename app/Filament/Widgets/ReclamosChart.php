<?php

namespace App\Filament\Widgets;

use App\Models\Reclamo;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReclamosChart extends ChartWidget
{
    protected static ?string $heading = 'Reclamos por Mes';
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $reclamos = Reclamo::select(
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        $data = array_fill(0, 12, 0);
        
        foreach ($reclamos as $reclamo) {
            $data[$reclamo->mes - 1] = $reclamo->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Reclamos',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $meses,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}