{{-- resources/views/filament/infolists/entries/role-icon-display.blade.php --}}
<div class="flex justify-center">
    <div class="w-20 h-20 rounded-xl {{ in_array($getRecord()->name, ['admin', 'funcionario', 'ciudadano']) ? 'bg-gradient-to-br' : 'bg-gray-100 dark:bg-gray-800' }}"
         style="{{ !in_array($getRecord()->name, ['admin', 'funcionario', 'ciudadano']) && $getRecord()->color ? 'background-color: ' . $getRecord()->color . '20' : '' }}
                {{ in_array($getRecord()->name, ['admin']) ? 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : '' }}
                {{ in_array($getRecord()->name, ['funcionario']) ? 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : '' }}
                {{ in_array($getRecord()->name, ['ciudadano']) ? 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)' : '' }}">
        <div class="w-full h-full flex items-center justify-center">
            <span class="text-3xl">
                @switch($getRecord()->name)
                    @case('admin')
                        🛡️
                        @break
                    @case('funcionario')
                        💼
                        @break
                    @case('ciudadano')
                        👤
                        @break
                    @default
                        🔑
                @endswitch
            </span>
        </div>
    </div>
</div>