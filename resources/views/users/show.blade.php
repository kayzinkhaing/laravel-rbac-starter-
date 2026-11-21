@extends('layouts.app')

@section('title', 'User details')
@section('description', 'Review this account and its effective access.')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('role.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="h-4 w-4" /> Back to users</a>
    <div class="flex gap-2">
        @can('update', $user)
            <a href="{{ route('role.users.edit', ['user' => $user]) }}" class="btn-secondary"><x-icon name="pencil" class="h-4 w-4" /> Edit user</a>
        @endcan
        @if (!auth()->user()->is($user))
            @can('delete', $user)
                <form action="{{ route('role.users.destroy', ['user' => $user]) }}" method="POST" onsubmit="return confirm('Delete this account? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger"><x-icon name="trash-2" class="h-4 w-4" /> Delete</button>
                </form>
            @endcan
        @endif
    </div>
</div>

<div class="grid gap-5 lg:grid-cols-[minmax(0,1.1fr)_minmax(18rem,0.9fr)]">
    <section class="surface-card p-5 sm:p-6" aria-labelledby="user-profile-heading">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-5">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-lg font-semibold text-indigo-700">{{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}</span>
            <div class="min-w-0">
                <h2 id="user-profile-heading" class="truncate text-lg font-semibold text-slate-900">{{ $user->name }}</h2>
                <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
            </div>
        </div>
        <dl class="grid gap-x-6 gap-y-5 pt-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Email status</dt>
                <dd class="mt-1.5 text-sm font-medium text-slate-700">
                    @if ($user->email_verified_at)
                        <span class="inline-flex items-center gap-1.5 text-emerald-700"><x-icon name="check-circle" class="h-4 w-4" /> Verified</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-amber-700"><x-icon name="clock-3" class="h-4 w-4" /> Pending verification</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Member since</dt>
                <dd class="mt-1.5 text-sm text-slate-700">{{ $user->created_at?->format('F j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Last updated</dt>
                <dd class="mt-1.5 text-sm text-slate-700">{{ $user->updated_at?->diffForHumans() }}</dd>
            </div>
        </dl>
        <div class="mt-6 border-t border-slate-100 pt-5">
            <h3 class="text-sm font-semibold text-slate-900">Assigned roles</h3>
            @if ($user->roles->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($user->roles as $role)
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700"><x-icon name="shield" class="h-4 w-4 text-slate-400" /> {{ \Illuminate\Support\Str::headline($role->name) }}</span>
                    @endforeach
                </div>
            @else
                <p class="mt-2 text-sm text-slate-500">No roles assigned.</p>
            @endif
        </div>
    </section>

    <section class="surface-card p-5 sm:p-6" aria-labelledby="effective-permissions-heading">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 id="effective-permissions-heading" class="text-sm font-semibold text-slate-900">Effective permissions</h2>
                <p class="mt-1 text-sm text-slate-500">Permissions inherited from assigned roles and direct grants.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $user->getAllPermissions()->count() }}</span>
        </div>
        @php($permissions = $user->getAllPermissions()->sortBy('name'))
        @if ($permissions->isNotEmpty())
            <ul class="mt-4 max-h-[28rem] space-y-2 overflow-y-auto">
                @foreach ($permissions as $permission)
                    <li class="flex items-start gap-2.5 rounded-lg bg-slate-50 px-3 py-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />
                        <span class="min-w-0">
                            <span class="block break-words text-sm font-medium text-slate-700">{{ $permission->name }}</span>
                            @php($inheritedRoles = $user->roles->filter(fn ($role) => $role->permissions->contains('id', $permission->id))->pluck('name'))
                            @if ($inheritedRoles->isNotEmpty())
                                <span class="mt-0.5 block text-xs text-slate-500">Via {{ $inheritedRoles->map(fn ($role) => \Illuminate\Support\Str::headline($role))->join(', ') }}</span>
                            @else
                                <span class="mt-0.5 block text-xs text-slate-500">Direct assignment</span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="mt-5 rounded-lg border border-dashed border-slate-200 px-4 py-8 text-center">
                <p class="text-sm font-medium text-slate-700">No effective permissions</p>
                <p class="mt-1 text-xs text-slate-500">Assign a role to grant this user access.</p>
            </div>
        @endif
    </section>
</div>
@endsection
