<?php

namespace App\Filament\Ciudadano\Widgets;

use Filament\Widgets\Widget;

class AlertaPerfilIncompleto extends Widget
{
    protected static string $view = 'filament.ciudadano.widgets.alerta-perfil-incompleto';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = -1; // Aparece primero

    public static function canView(): bool
    {
        $ciudadano = auth()->user()->ciudadano;
        
        // Solo mostrar si el perfil NO está completo
        return $ciudadano && !$ciudadano->perfil_completo;
    }
}