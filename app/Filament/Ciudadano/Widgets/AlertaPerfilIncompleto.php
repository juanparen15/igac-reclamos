<?php

namespace App\Filament\Ciudadano\Widgets;

use App\Models\Ciudadano;
use Filament\Widgets\Widget;

class AlertaPerfilIncompleto extends Widget
{
    protected static string $view = 'filament.ciudadano.widgets.alerta-perfil-incompleto';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // $ciudadano = Ciudadano::where('user_id', auth()->id())->first();
        // return !$ciudadano || !$ciudadano->perfil_completo;

        $ciudadano = auth()->user()->ciudadano;
        return !$ciudadano || !$ciudadano->perfil_completo;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->can('ver_estadisticas');
    }
}
