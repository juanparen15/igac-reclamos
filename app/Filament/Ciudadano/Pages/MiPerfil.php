<?php

namespace App\Filament\Ciudadano\Pages;

use Filament\Pages\Page;

class MiPerfil extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.ciudadano.pages.mi-perfil';
    protected static ?string $title = 'Mi Perfil';
    
    // ✅ Ocultar de la navegación
    protected static bool $shouldRegisterNavigation = false;
    
    // O elimina todo este archivo si ya no lo vas a usar
}