<?php

namespace App\Filament\Ciudadano\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Ciudadano;
use Filament\Notifications\Notification;
use App\Filament\Ciudadano\Resources\PerfilCiudadanoResource;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected ?Ciudadano $ciudadano = null;

    public function mount(): void
    {
        // parent::mount();

        // Cargar ciudadano una sola vez
        $this->ciudadano = auth()->user()->ciudadano;

        // Mostrar notificación si el perfil está incompleto
        if ($this->ciudadano && !$this->ciudadano->perfil_completo) {
            Notification::make()
                ->warning()
                ->title('Perfil Incompleto')
                ->body('Complete su perfil para poder crear y gestionar reclamos en el sistema.')
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('completar')
                        ->label('Completar Ahora')
                        ->button()
                        ->color('warning')
                        ->url(PerfilCiudadanoResource::getUrl('edit', ['record' => $this->ciudadano->id])),
                ])
                ->send();
        }
    }

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
        if (!$this->ciudadano) {
            $this->ciudadano = auth()->user()->ciudadano;
        }

        if ($this->ciudadano && $this->ciudadano->primer_nombre) {
            return 'Bienvenido, ' . $this->ciudadano->primer_nombre;
        }

        return 'Bienvenido';
    }

    public function getSubheading(): ?string
    {
        if (!$this->ciudadano) {
            $this->ciudadano = auth()->user()->ciudadano;
        }

        if (!$this->ciudadano || !$this->ciudadano->perfil_completo) {
            return 'Complete su perfil para poder crear reclamos';
        }

        return 'Panel de control de reclamos';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Ciudadano\Widgets\AlertaPerfilIncompleto::class,  // ✅ Widget de alerta
            \App\Filament\Ciudadano\Widgets\MisReclamosResumen::class,
            \App\Filament\Ciudadano\Widgets\EstadoMisReclamos::class,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }
}