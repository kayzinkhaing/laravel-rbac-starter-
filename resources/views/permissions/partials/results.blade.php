<div class="surface-card overflow-hidden">
    @if ($permissions->count())
        @php($permissionGroups = $permissions->getCollection()->groupBy(fn ($permission) => \Illuminate\Support\Str::headline(\Illuminate\Support\Str::before($permission->name, '.'))))
        @foreach ($permissionGroups as $module => $group)
            <div class="border-b border-slate-100 last:border-b-0">
                <div class="flex items-center justify-between bg-slate-50/70 px-5 py-3 sm:px-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $module }}</h3>
                    <span class="text-xs text-slate-400">{{ $group->count() }} {{ \Illuminate\Support\Str::plural('permission', $group->count()) }}</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($group as $permission)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><x-icon name="key-round" class="h-4 w-4" /></span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-slate-800">{{ $permission->name }}</p>
                                    <p class="text-xs text-slate-500">{{ number_format($permission->roles_count) }} {{ \Illuminate\Support\Str::plural('role', $permission->roles_count) }} assigned</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @can('update', $permission)
                                    <a href="{{ route('role.permissions.edit', ['permission' => $permission]) }}" class="icon-button" aria-label="Edit {{ $permission->name }}"><x-icon name="pencil" class="h-4 w-4" /></a>
                                @endcan
                                @can('delete', $permission)
                                    <form action="{{ route('role.permissions.destroy', ['permission' => $permission]) }}" method="POST" onsubmit="return confirm('Delete this permission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-button text-slate-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Delete {{ $permission->name }}"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3 text-sm text-slate-500 sm:px-6">
            <span>Showing {{ $permissions->firstItem() }}–{{ $permissions->lastItem() }} of {{ $permissions->total() }}</span>
            @if ($permissions->hasPages())
                {{ $permissions->links() }}
            @endif
        </div>
    @else
        <div class="px-6 py-16 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><x-icon name="key-round" class="h-6 w-6" /></span>
            <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $search ? 'No permissions match your search' : 'No permissions created' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $search ? 'Try a different search term.' : 'Create a permission to define an individual access rule.' }}</p>
        </div>
    @endif
</div>
