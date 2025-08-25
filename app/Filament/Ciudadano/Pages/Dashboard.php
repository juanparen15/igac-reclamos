<?php

namespace App\Filament\Ciudadano\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Ciudadano;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 3,
        ];
    }

    public function getTitle(): string
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

        if ($ciudadano) {
            return 'Bienvenido, ' . $ciudadano->primer_nombre;
        }

        return 'Bienvenido';
    }

    public function getSubheading(): ?string
    {
        $ciudadano = Ciudadano::where('user_id', auth()->id())->first();

        if (!$ciudadano || !$ciudadano->perfil_completo) {
            return 'Complete su perfil para poder crear reclamos';
        }

        return 'Panel de control de reclamos';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Ciudadano\Widgets\MisReclamosResumen::class,
            \App\Filament\Ciudadano\Widgets\EstadoMisReclamos::class,
        ];
    }
}
