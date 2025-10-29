<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;

class PolicyFixerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Escuchar cuando termine shield:generate
        Event::listen(CommandFinished::class, function ($event) {
            if ($event->command === 'shield:generate') {
                $this->info('Reparación automática políticas Ciudadano...');
                Artisan::call('policies:arreglar-ciudadano-reclamo');
                $this->info('Políticas Arregladas Automaticamente');
            }
        });
    }

    protected function info(string $message): void
    {
        if (app()->runningInConsole()) {
            echo PHP_EOL . $message . PHP_EOL;
        }
    }
}