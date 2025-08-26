{{-- resources/views/filament/tables/columns/permissions-badges.blade.php --}}
<div class="flex flex-wrap gap-1 mt-2">
    @php
        $permissions = $getRecord()->permissions;
        $displayLimit = 4;
        $totalCount = $permissions->count();
    @endphp
    
    @forelse($permissions->take($displayLimit) as $permission)
        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-400">
            {{ str_replace('_', ' ', $permission->name) }}
        </span>
    @empty
        <span class="text-sm text-gray-500 italic">Sin permisos asignados</span>
    @endforelse
    
    @if($totalCount > $displayLimit)
        <span class="inline-flex items-center rounded-md bg-primary-100 dark:bg-primary-900 px-2 py-0.5 text-xs font-medium text-primary-600 dark:text-primary-400">
            +{{ $totalCount - $displayLimit }} más
        </span>
    @endif
</div>