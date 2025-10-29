<x-filament-widgets::widget>
    <x-filament::section
        :icon="'heroicon-o-exclamation-triangle'"
        icon-color="warning"
    >
        <div class="space-y-4">
            {{-- Encabezado --}}
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-16 h-16 bg-warning-100 dark:bg-warning-900/20 rounded-full">
                        <svg class="w-8 h-8 text-warning-600 dark:text-warning-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>
                
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        ¡Atención! Perfil Incompleto
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Debe completar su perfil para poder crear y gestionar reclamos en el sistema.
                    </p>
                </div>
            </div>

            {{-- Campos faltantes --}}
            @php
                $ciudadano = auth()->user()->ciudadano;
                $camposFaltantes = [];
                
                if (empty($ciudadano->primer_nombre)) $camposFaltantes[] = 'Primer Nombre';
                if (empty($ciudadano->primer_apellido)) $camposFaltantes[] = 'Primer Apellido';
                if (empty($ciudadano->numero_celular)) $camposFaltantes[] = 'Número de Celular';
                if (empty($ciudadano->genero)) $camposFaltantes[] = 'Género';
                if (empty($ciudadano->departamento_id)) $camposFaltantes[] = 'Departamento';
                if (empty($ciudadano->ciudad_id)) $camposFaltantes[] = 'Ciudad';
                
                $progreso = round(((6 - count($camposFaltantes)) / 6) * 100);
            @endphp

            {{-- Barra de progreso --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Progreso del perfil</span>
                    <span class="font-bold text-warning-600 dark:text-warning-400">{{ $progreso }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div 
                        class="bg-warning-500 h-3 rounded-full transition-all duration-500 ease-out"
                        style="width: {{ $progreso }}%"
                    ></div>
                </div>
            </div>

            @if(count($camposFaltantes) > 0)
                <div class="bg-warning-50 dark:bg-warning-900/10 border border-warning-200 dark:border-warning-800 rounded-lg p-4">
                    <p class="text-sm font-medium text-warning-800 dark:text-warning-300 mb-3">
                        Campos requeridos pendientes ({{ count($camposFaltantes) }}):
                    </p>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($camposFaltantes as $campo)
                            <li class="flex items-center text-sm text-warning-700 dark:text-warning-400">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ $campo }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Botón de acción --}}
            <div class="flex justify-end pt-2">
                <a 
                    href="{{ \App\Filament\Ciudadano\Resources\PerfilCiudadanoResource::getUrl('edit', ['record' => auth()->user()->ciudadano->id]) }}" 
                    class="inline-flex items-center px-6 py-3 bg-warning-600 hover:bg-warning-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl"
                >
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Completar Mi Perfil Ahora
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>