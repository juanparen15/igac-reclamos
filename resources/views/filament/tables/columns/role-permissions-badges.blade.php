{{-- resources/views/filament/tables/columns/role-permissions-badges.blade.php --}}
@php
    $permissions = $getRecord()->permissions;
    $displayLimit = 4;
    $displayPermissions = $permissions->take($displayLimit);
    $remainingCount = $permissions->count() - $displayLimit;
@endphp

<div class="flex flex-wrap gap-1.5">
    @forelse($displayPermissions as $permission)
        @php
            $icon = match(true) {
                str_contains($permission->name, 'gestionar') => '⚙️',
                str_contains($permission->name, 'crear') => '➕',
                str_contains($permission->name, 'ver') => '👁️',
                str_contains($permission->name, 'resolver') => '✅',
                str_contains($permission->name, 'asignar') => '👤',
                default => '🔑'
            };
            
            $color = match(true) {
                str_contains($permission->name, 'admin') => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                str_contains($permission->name, 'gestionar') => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                str_contains($permission->name, 'crear') => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                str_contains($permission->name, 'ver') => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
            };
        @endphp
        
        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">
            <span>{{ $icon }}</span>
            <span>{{ str_replace('_', ' ', $permission->name) }}</span>
        </span>
    @empty
        <span class="text-sm text-gray-500 dark:text-gray-400 italic">Sin permisos asignados</span>
    @endforelse
    
    @if($remainingCount > 0)
        <span class="inline-flex items-center gap-1 rounded-full bg-primary-100 dark:bg-primary-900 px-2.5 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-300">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ $remainingCount }} más
        </span>
    @endif
</div>