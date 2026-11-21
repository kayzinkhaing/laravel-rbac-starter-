@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'A permission-scoped overview of your administration workspace.')

@section('content')
<section aria-labelledby="welcome-heading">
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">{{ now()->format('l, F j, Y') }}</p>
            <h2 id="welcome-heading" class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }}
            </h2>
        </div>
        @can('viewAny', \App\Models\User::class)
            <a href="{{ route('role.users.index') }}" class="btn-primary">
                <x-icon name="users" class="h-4 w-4" /> Manage users
            </a>
        @endcan
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <article class="surface-card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stat['value']) }}</p>
                    </div>
                    <span @class([
                        'flex h-10 w-10 items-center justify-center rounded-lg',
                        'bg-indigo-50 text-indigo-600' => $stat['tone'] === 'indigo',
                        'bg-emerald-50 text-emerald-600' => $stat['tone'] === 'emerald',
                        'bg-amber-50 text-amber-600' => $stat['tone'] === 'amber',
                        'bg-violet-50 text-violet-600' => $stat['tone'] === 'violet',
                    ])>
                        <x-icon :name="$stat['icon']" class="h-5 w-5" />
                    </span>
                </div>
            </article>
        @endforeach
    </div>

    <div @class([
        'mt-6 grid gap-6',
        'xl:grid-cols-[minmax(0,1.55fr)_minmax(18rem,0.8fr)]' => $canManageUsers,
    ])>
        @if ($canManageUsers)
            <section class="surface-card min-w-0" aria-labelledby="registrations-heading">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="registrations-heading" class="text-sm font-semibold text-slate-900">Recent registrations</h2>
                    <p class="mt-1 text-xs text-slate-500">The latest accounts created in your workspace</p>
                </div>
                @can('viewAny', \App\Models\User::class)
                    <a href="{{ route('role.users.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        View users <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @endcan
            </div>
            @if ($recentUsers->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[34rem] text-left">
                        <thead>
                            <tr class="table-heading border-b border-slate-100">
                                <th scope="col" class="px-5 py-3 font-medium sm:px-6">User</th>
                                <th scope="col" class="px-5 py-3 font-medium">Status</th>
                                <th scope="col" class="px-5 py-3 font-medium text-right sm:px-6">Registered</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentUsers as $user)
                                <tr class="transition-colors hover:bg-slate-50/70">
                                    <td class="px-5 py-3.5 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                                                {{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-medium text-slate-800">{{ $user->name }}</p>
                                                <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($user->email_verified_at)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right text-sm text-slate-500 sm:px-6">{{ $user->created_at?->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-14 text-center">
                    <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-icon name="users" class="h-5 w-5" /></span>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900">No registrations yet</h3>
                    <p class="mt-1 text-sm text-slate-500">New accounts will appear here as they join.</p>
                </div>
            @endif
            </section>
        @endif

        <section class="surface-card" aria-labelledby="activity-heading">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 id="activity-heading" class="text-sm font-semibold text-slate-900">Administrative activity</h2>
                <p class="mt-1 text-xs text-slate-500">Recent changes across your workspace</p>
            </div>
            <div class="px-6 py-10 text-center">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-icon name="activity" class="h-5 w-5" /></span>
                <h3 class="mt-3 text-sm font-medium text-slate-800">Activity tracking is not enabled</h3>
                <p class="mx-auto mt-1.5 max-w-xs text-sm leading-6 text-slate-500">This workspace does not currently record administrative activity.</p>
            </div>
        </section>
    </div>
</section>
@endsection
