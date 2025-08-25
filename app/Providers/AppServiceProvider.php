<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Responses\RegistrationResponse;
use App\Models\Ciudadano;
use App\Models\Reclamo;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar la respuesta personalizada para el panel ciudadano
        // $this->app->bind(RegistrationResponseContract::class, RegistrationResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers
        // \App\Models\User::observe(\App\Observers\UserObserver::class);
        Reclamo::observe(\App\Observers\ReclamoObserver::class);
        Ciudadano::observe(\App\Observers\CiudadanoObserver::class);

         // Log para verificar que se carga
        // Log::info('AppServiceProvider boot - Observer registrado');
    }
}
