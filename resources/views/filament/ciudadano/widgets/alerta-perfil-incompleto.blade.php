{{-- <x-filament::section class="bg-warning-50 border-warning-300">
    <div class="flex items-center space-x-3">
        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-warning-600" />
        <div>
            <h3 class="text-lg font-medium text-warning-900">Perfil Incompleto</h3>
            <p class="text-sm text-warning-700 mt-1">
                Debe completar su perfil antes de poder crear reclamos.
            </p>
            <x-filament::button
                href="{{ \App\Filament\Ciudadano\Resources\PerfilCiudadanoResource::getUrl('edit', ['record' => auth()->user()->ciudadano]) }}"
                tag="a"
                class="mt-3"
                size="sm"
            >
                Completar Perfil
            </x-filament::button>
        </div>
    </div>
</x-filament::section> --}}

{{-- resources/views/filament/ciudadano/widgets/alerta-perfil-incompleto.blade.php --}}
<x-filament::section class="bg-warning-50 border-warning-300">
    <div class="flex items-center space-x-3">
        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-warning-600" />
        <div>
            <h3 class="text-lg font-medium text-warning-900">Perfil Incompleto</h3>
            <p class="text-sm text-warning-700 mt-1">
                Debe completar su perfil antes de poder crear reclamos.
            </p>
            <x-filament::button
                href="{{ \App\Filament\Ciudadano\Resources\PerfilCiudadanoResource::getUrl('index') }}"
                tag="a"
                class="mt-3"
                size="sm"
            >
                Completar Perfil
            </x-filament::button>
        </div>
    </div>
</x-filament::section>