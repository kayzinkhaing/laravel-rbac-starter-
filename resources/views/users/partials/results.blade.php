<div class="surface-card overflow-hidden">
    @if ($users->count())
        <div class="overflow-x-auto">
            <table class="w-full min-w-[48rem] text-left">
                <thead>
                    <tr class="table-heading border-b border-slate-100">
                        @foreach (['name' => 'User', 'roles_count' => 'Role', 'email_verified_at' => 'Email status', 'created_at' => 'Joined'] as $field => $label)
                            @php($currentSort = $filters['sort'] ?? 'created_at')
                            @php($currentDirection = $filters['direction'] ?? 'desc')
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
                    @foreach ($users as $user)
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-5 py-3.5 sm:px-6">
                                <a href="{{ route('role.users.show', ['user' => $user]) }}" class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-semibold text-indigo-700">{{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}</span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium text-slate-800 hover:text-indigo-700">{{ $user->name }}</span>
                                        <span class="block truncate text-xs text-slate-500">{{ $user->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                @forelse ($user->roles->take(2) as $role)
                                    <span class="mr-1 inline-flex rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                                @empty
                                    <span class="text-sm text-slate-400">No role</span>
                                @endforelse
                                @if ($user->roles_count > 2)
                                    <span class="text-xs text-slate-500">+{{ $user->roles_count - 2 }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($user->email_verified_at)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Verified</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Pending</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-sm text-slate-500">{{ $user->created_at?->format('M j, Y') }}</td>
                            <td class="px-5 py-3.5 sm:px-6">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('role.users.show', ['user' => $user]) }}" class="icon-button" aria-label="View {{ $user->name }}"><x-icon name="arrow-up-right" class="h-4 w-4" /></a>
                                    @can('update', $user)
                                        <a href="{{ route('role.users.edit', ['user' => $user]) }}" class="icon-button" aria-label="Edit {{ $user->name }}"><x-icon name="pencil" class="h-4 w-4" /></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3 text-sm text-slate-500 sm:px-6">
            <span>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
            {{ $users->links() }}
        </div>
    @else
        <div class="px-6 py-16 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><x-icon name="users" class="h-6 w-6" /></span>
            <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ count(array_filter($filters)) ? 'No users match these filters' : 'No users yet' }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ count(array_filter($filters)) ? 'Adjust your search or filters and try again.' : 'Accounts will appear here when they are created.' }}</p>
            @if (count(array_filter($filters)))
                <a href="{{ route('role.users.index') }}" class="mt-4 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-700">Clear filters</a>
            @endif
        </div>
    @endif
</div>
