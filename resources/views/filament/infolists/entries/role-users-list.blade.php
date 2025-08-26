{{-- resources/views/filament/infolists/entries/role-users-list.blade.php --}}
<div class="space-y-2">
    @forelse($getRecord()->users()->limit(10)->get() as $user)
        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=FFFFFF&background=' . substr(md5($user->name), 0, 6) }}" 
                 alt="{{ $user->name }}"
                 class="w-10 h-10 rounded-full">
            <div class="flex-1">
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>
            <a href="{{ route('filament.admin.resources.users.edit', $user) }}" 
               class="text-primary-600 hover:text-primary-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <p>No hay usuarios con este rol</p>
        </div>
    @endforelse
    
    @if($getRecord()->users()->count() > 10)
        <div class="text-center pt-2">
            <a href="{{ route('filament.admin.resources.users.index', ['tableFilters[roles][values][0]' => $getRecord()->id]) }}" 
               class="text-sm text-primary-600 hover:text-primary-500 font-medium">
                Ver todos los {{ $getRecord()->users()->count() }} usuarios →
            </a>
        </div>
    @endif
</div>