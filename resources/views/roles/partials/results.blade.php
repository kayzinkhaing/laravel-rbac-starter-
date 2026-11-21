<div class="surface-card overflow-hidden">
    @if ($roles->count())
        <div class="overflow-x-auto">
            <table class="w-full min-w-[42rem] text-left">
                <thead>
                    <tr class="table-heading border-b border-slate-100">
                        @foreach (['name' => 'Role', 'users_count' => 'Users', 'permissions_count' => 'Permissions'] as $field => $label)
                            @php($currentSort = $filters['sort'] ?? 'name')
                            @php($currentDirection = $filters['direction'] ?? 'asc')
                            <th scope="col" class="px-5 py-3.5 font-medium sm:px-6" aria-sort="{{ $currentSort === $field ? ($currentDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                <button type="button" class="inline-flex items-center gap-1.5 hover:text-slate-900" data-sort="{{ $field }}">
                                    {{ $label }}
                                    @if ($currentSort === $field)
                                        <x-icon :name="$currentDirection === 'asc' ? 'chevron-up' : 'chevron-down'" class="h-3.5 w-3.5" />
                                    @endif
                                </button>
                            </th>
                        @endforeach
                        <th scope="col" class="px-5 py-3.5 text-right font-medium sm:px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($roles as $role)
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><x-icon name="shield" class="h-[18px] w-[18px]" /></span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">{{ \Illuminate\Support\Str::headline($role->name) }}</p>
                                        <p class="text-xs text-slate-500">{{ $role->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ number_format($role->users_count) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex max-w-lg flex-wrap gap-1.5">
                                    @forelse ($role->permissions->take(3) as $permission)
                                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-sm text-slate-400">No permissions assigned</span>
                                    @endforelse
                                    @if ($role->permissions_count > $role->permissions->count())
                                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-500">+{{ $role->permissions_count - $role->permissions->count() }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('update', $role)
                                        <a href="{{ route('role.roles.edit', ['role' => $role]) }}" class="icon-button" aria-label="Edit {{ $role->name }}"><x-icon name="pencil" class="h-4 w-4" /></a>
                                    @endcan
                                    @can('delete', $role)
                                        <form action="{{ route('role.roles.destroy', ['role' => $role]) }}" method="POST" onsubmit="return confirm('Delete this role? Users assigned to it will lose this role.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-button text-slate-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Delete {{ $role->name }}"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($roles->hasPages())
            <div class="border-t border-slate-100 px-5 py-3 sm:px-6">{{ $roles->links() }}</div>
        @endif
    @else
        <div class="px-6 py-16 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><x-icon name="shield" class="h-6 w-6" /></span>
            <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ !empty($filters['search']) ? 'No roles match your search' : 'No roles created' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ !empty($filters['search']) ? 'Try another role name.' : 'Create a role to define a reusable set of access permissions.' }}</p>
            @can('create', \App\Models\Role::class)
                <a href="{{ route('role.roles.create') }}" class="btn-primary mt-5"><x-icon name="plus" class="h-4 w-4" /> Create role</a>
            @endcan
        </div>
    @endif
</div>
