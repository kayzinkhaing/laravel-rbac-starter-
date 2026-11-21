@extends('layouts.public')

@section('title', 'Home')

@section('content')
<section class="relative overflow-hidden border-b border-slate-200 bg-slate-50">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[minmax(0,1.1fr)_minmax(22rem,0.9fr)] lg:px-8 lg:py-28">
        <div class="max-w-2xl">
            <p class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700">
                <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                User management and access control
            </p>
            <h1 class="mt-6 text-4xl font-semibold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-[3.5rem]">
                Clear access.<br class="hidden sm:block"> Confident teams.
            </h1>
            <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                Manage user accounts, roles, and permissions in one focused workspace. Give every account the access it needs - and no more.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="btn-primary">
                        Create an account <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary">Sign in</a>
                @else
                    <a href="{{ \App\Support\AdminDashboardAccess::profileUrlFor(auth()->user()) }}" class="btn-primary">
                        View your profile <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    @if (\App\Support\AdminDashboardAccess::assignedRoles(auth()->user())->isNotEmpty())
                        <a href="{{ \App\Support\AdminDashboardAccess::dashboardUrlFor(auth()->user()) }}" class="btn-secondary">Open admin workspace</a>
                    @endif
                @endguest
            </div>
        </div>

        <div class="surface-card p-5 sm:p-7">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Access, organized</p>
                    <p class="mt-1 text-xs text-slate-500">A reusable foundation for your team</p>
                </div>
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <x-icon name="shield" class="h-5 w-5" />
                </span>
            </div>
            <ul class="mt-2 divide-y divide-slate-100">
                <li class="flex items-start gap-3 py-4">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="users" class="h-4 w-4" /></span>
                    <span><span class="block text-sm font-medium text-slate-800">User directory</span><span class="mt-1 block text-sm leading-5 text-slate-500">Manage accounts, profiles, and assigned access.</span></span>
                </li>
                <li class="flex items-start gap-3 py-4">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="shield" class="h-4 w-4" /></span>
                    <span><span class="block text-sm font-medium text-slate-800">Role-based access</span><span class="mt-1 block text-sm leading-5 text-slate-500">Group permissions into roles that match responsibilities.</span></span>
                </li>
                <li class="flex items-start gap-3 py-4">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><x-icon name="key-round" class="h-4 w-4" /></span>
                    <span><span class="block text-sm font-medium text-slate-800">Permission catalog</span><span class="mt-1 block text-sm leading-5 text-slate-500">Review and manage the access rules used throughout the app.</span></span>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
    <div class="grid gap-8 md:grid-cols-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Account first</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight text-slate-900">A profile for every user</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">People can securely review and update their own account information.</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Least privilege</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight text-slate-900">Access follows permissions</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Administrative pages and actions are authorized on the server.</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Built to extend</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight text-slate-900">Reusable Laravel architecture</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Blade, Tailwind, Alpine, policies, and services keep the system adaptable.</p>
        </div>
    </div>
</section>
@endsection
