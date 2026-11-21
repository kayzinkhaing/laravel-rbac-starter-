@extends('layouts.app')

@section('title', 'Permissions')
@section('description', 'Browse access permissions across your workspace.')

@section('content')
<section aria-labelledby="permissions-heading">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 id="permissions-heading" class="text-lg font-semibold tracking-tight text-slate-900">Permission catalog</h2>
            <p class="mt-1 text-sm text-slate-500">Review permissions and the roles that currently use them.</p>
        </div>
        @can('create', \App\Models\Permission::class)
            <a href="{{ route('role.permissions.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Create permission</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('role.permissions.index') }}" data-live-search data-results="#permissions-results" data-default-sort="name" data-default-direction="asc" class="surface-card mb-4 flex flex-col gap-3 p-4 sm:flex-row">
        <div class="relative min-w-0 flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="search" value="{{ $search }}" class="input-control pl-9" placeholder="Search permissions" aria-label="Search permissions" data-live-input>
        </div>
        <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'name' }}">
        <input type="hidden" name="direction" value="{{ $filters['direction'] ?? 'asc' }}">
        <button type="submit" class="sr-only">Search permissions</button>
        <button type="button" class="btn-secondary" data-live-clear>Clear search</button>
    </form>
    <p class="mb-2 min-h-5 text-sm text-slate-500" role="status" aria-live="polite" data-live-status></p>
    <div id="permissions-results" data-live-results aria-busy="false">
        @include('permissions.partials.results')
    </div>
</section>
@endsection
