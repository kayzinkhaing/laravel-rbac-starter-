@extends('layouts.app')

@section('title', 'Create role')
@section('description', 'Define a role and choose its access permissions.')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('role.roles.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="h-4 w-4" /> Back to roles</a>
    <form action="{{ route('role.roles.store') }}" method="POST" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <section class="surface-card p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-slate-900">Role details</h2>
            <p class="mt-1 text-sm text-slate-500">Give this role a clear name that describes its access level.</p>
            <div class="mt-5">
                <label for="name" class="field-label">Role name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="input-control" placeholder="e.g. content editor" required autofocus>
                <x-input-error :messages="$errors->get('name')" />
            </div>
        </section>

        <section class="surface-card overflow-hidden" x-data="{ search: '' }">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Permissions</h2>
                        <p class="mt-1 text-sm text-slate-500">Choose what members with this role can do.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="btn-secondary min-h-9 px-3 text-xs" @click="$el.closest('form').querySelectorAll('.permission-checkbox').forEach(input => input.checked = true)">Select all</button>
                        <button type="button" class="btn-secondary min-h-9 px-3 text-xs" @click="$el.closest('form').querySelectorAll('.permission-checkbox').forEach(input => input.checked = false)">Clear</button>
                    </div>
                </div>
                <div class="relative mt-4">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" x-model="search" class="input-control pl-9" placeholder="Search permissions" aria-label="Search permissions">
                </div>
            </div>
            <div class="max-h-[28rem] space-y-5 overflow-y-auto p-5 sm:p-6">
                @php($permissionGroups = $permissions->groupBy(fn ($permission) => \Illuminate\Support\Str::headline(\Illuminate\Support\Str::before($permission->name, '.'))))
                @forelse ($permissionGroups as $module => $group)
                    <fieldset>
                        <legend class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $module }}</legend>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($group as $permission)
                                <label class="permission-option flex cursor-pointer items-start gap-3 rounded-lg border border-slate-100 p-3 transition hover:border-indigo-200 hover:bg-indigo-50/30" data-permission="{{ strtolower($permission->name) }}" x-show="!search || $el.dataset.permission.includes(search.toLowerCase())">
                                    <input type="checkbox" class="permission-checkbox mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', [])))>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-slate-700">{{ \Illuminate\Support\Str::after($permission->name, '.') === $permission->name ? \Illuminate\Support\Str::headline($permission->name) : \Illuminate\Support\Str::headline(\Illuminate\Support\Str::after($permission->name, '.')) }}</span>
                                        <span class="mt-0.5 block truncate text-xs text-slate-400">{{ $permission->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">No permissions are available yet.</p>
                @endforelse
                <x-input-error :messages="$errors->get('permissions')" />
            </div>
        </section>
        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('role.roles.index') }}" class="btn-secondary">Cancel</a>
            <x-loading-button loading="Creating role">Create role</x-loading-button>
        </div>
    </form>
</div>
@endsection
