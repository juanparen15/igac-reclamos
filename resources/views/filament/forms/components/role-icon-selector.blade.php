{{-- resources/views/filament/forms/components/role-icon-selector.blade.php --}}
<div class="flex justify-center mb-4">
    <div class="w-24 h-24 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
        <span class="text-4xl">
            @if($getRecord() && in_array($getRecord()->name, ['admin', 'funcionario', 'ciudadano']))
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
                @endswitch
            @else
                🔑
            @endif
        </span>
    </div>
</div>