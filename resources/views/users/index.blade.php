@extends('layouts.app')

@section('title', 'Users')
@section('description', 'Manage workspace accounts and their access.')

@section('content')
<section aria-labelledby="users-heading">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 id="users-heading" class="text-lg font-semibold tracking-tight text-slate-900">All users</h2>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($users->total()) }} {{ \Illuminate\Support\Str::plural('account', $users->total()) }} in your workspace</p>
        </div>
        @can('create', \App\Models\User::class)
            <a href="{{ route('role.users.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add user</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('role.users.index') }}" data-live-search data-results="#users-results" data-default-sort="created_at" data-default-direction="desc" class="surface-card mb-4 grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(15rem,1fr)_13rem_13rem_auto_auto]">
        <div class="relative min-w-0">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" class="input-control pl-9" placeholder="Search name, email or role" aria-label="Search users" data-live-input>
        </div>
        <select name="role" class="input-control" aria-label="Filter by role" data-live-filter>
            <option value="">All roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((string) ($filters['role'] ?? '') === (string) $role->id)>{{ \Illuminate\Support\Str::headline($role->name) }}</option>
            @endforeach
        </select>
        <select name="verified" class="input-control" aria-label="Filter by verification status" data-live-filter>
            <option value="">Any email status</option>
            <option value="verified" @selected(($filters['verified'] ?? '') === 'verified')>Verified</option>
            <option value="unverified" @selected(($filters['verified'] ?? '') === 'unverified')>Pending verification</option>
        </select>
        <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'created_at' }}">
        <input type="hidden" name="direction" value="{{ $filters['direction'] ?? 'desc' }}">
        <button type="submit" class="sr-only">Apply filters</button>
        <button type="button" class="btn-secondary" data-live-clear>Clear filters</button>
    </form>

    <p class="mb-2 min-h-5 text-sm text-slate-500" role="status" aria-live="polite" data-live-status></p>
    <div id="users-results" data-live-results aria-busy="false">
        @include('users.partials.results')
    </div>
</section>
@endsection
