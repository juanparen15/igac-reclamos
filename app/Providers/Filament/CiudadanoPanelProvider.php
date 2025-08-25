<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\EnsureUserIsCiudadano;
use App\Filament\Ciudadano\Pages\Auth\Register;
use Illuminate\Support\Facades\DB;

class CiudadanoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        if (config('app.debug')) {
            DB::listen(function ($query) {
                logger()->debug('SQL', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            });
        }
        return $panel
            ->id('ciudadano')
            ->path('ciudadano')
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('IGAC - Portal Ciudadano')
            ->discoverResources(in: app_path('Filament/Ciudadano/Resources'), for: 'App\\Filament\\Ciudadano\\Resources')
            ->discoverPages(in: app_path('Filament/Ciudadano/Pages'), for: 'App\\Filament\\Ciudadano\\Pages')
            ->pages([
                \App\Filament\Ciudadano\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Ciudadano/Widgets'), for: 'App\\Filament\\Ciudadano\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsCiudadano::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
