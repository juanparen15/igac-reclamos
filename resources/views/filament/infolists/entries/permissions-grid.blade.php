{{-- resources/views/filament/infolists/entries/permissions-grid.blade.php --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
    @forelse($getRecord()->permissions->sortBy('name') as $permission)
        <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ str_replace('_', ' ', ucfirst($permission->name)) }}
            </span>
        </div>
    @empty
        <div class="col-span-full text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <p>No hay permisos asignados a este rol</p>
        </div>
    @endforelse
</div>