@extends('layouts.app')

@section('title', 'Roles')
@section('description', 'Manage access roles and their permissions.')

@section('content')
<section aria-labelledby="roles-heading">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 id="roles-heading" class="text-lg font-semibold tracking-tight text-slate-900">All roles</h2>
            <p class="mt-1 text-sm text-slate-500">Control what people can access across your workspace.</p>
        </div>
        @can('create', \App\Models\Role::class)
            <a href="{{ route('role.roles.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Create role</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('role.roles.index') }}" data-live-search data-results="#roles-results" data-default-sort="name" data-default-direction="asc" class="surface-card mb-4 flex flex-col gap-3 p-4 sm:flex-row">
        <div class="relative min-w-0 flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" class="input-control pl-9" placeholder="Search roles" aria-label="Search roles" data-live-input>
        </div>
        <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'name' }}">
        <input type="hidden" name="direction" value="{{ $filters['direction'] ?? 'asc' }}">
        <button type="submit" class="sr-only">Search roles</button>
        <button type="button" class="btn-secondary" data-live-clear>Clear filters</button>
    </form>
    <p class="mb-2 min-h-5 text-sm text-slate-500" role="status" aria-live="polite" data-live-status></p>
    <div id="roles-results" data-live-results aria-busy="false">
        @include('roles.partials.results')
    </div>
</section>
@endsection
