@extends('layouts.app')

@section('title', 'Create role')
@section('description', 'Define a role and choose its access permissions.')

@section('content')
<div class="mx-auto max-w-3xl">
    <x-crud.page-heading title="Create role"
        description="Define a reusable access profile and select its permissions."
        :back-url="route('role.roles.index')" back-label="Back to roles" />
    <form action="{{ route('role.roles.store') }}" method="POST" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <x-crud.panel>
            <h2 class="text-sm font-semibold text-slate-900">Role details</h2>
            <p class="mt-1 text-sm text-slate-500">Choose a clear name that describes this access profile.</p>
            <div class="mt-5">
                <x-crud.field name="name" label="Role name" :value="old('name')"
                    placeholder="e.g. content editor" maxlength="255" is-required autofocus />
            </div>
        </x-crud.panel>

        <x-crud.panel :padding="false" x-data="{ search: '' }">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Permissions</h2>
                        <p class="mt-1 text-sm text-slate-500">Choose the actions members with this role can perform.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="btn-secondary min-h-9 px-3 text-xs" @click="$el.closest('form').querySelectorAll('.permission-checkbox').forEach(input => input.checked = true)">Select all</button>
                        <button type="button" class="btn-secondary min-h-9 px-3 text-xs" @click="$el.closest('form').querySelectorAll('.permission-checkbox').forEach(input => input.checked = false)">Clear</button>
                    </div>
                </div>
                <input type="search" x-model="search" class="input-control mt-4"
                    placeholder="Search permissions" aria-label="Search permissions">
            </div>
            <div class="max-h-[28rem] space-y-5 overflow-y-auto p-5 sm:p-6">
                @php($permissionGroups = $permissions->groupBy(fn ($permission) => \Illuminate\Support\Str::headline(\Illuminate\Support\Str::before($permission->name, '.'))))
                @forelse ($permissionGroups as $module => $group)
                    <fieldset>
                        <legend class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $module }}</legend>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($group as $permission)
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-100 p-3 transition hover:border-indigo-200 hover:bg-indigo-50/30"
                                    data-permission="{{ strtolower($permission->name) }}"
                                    x-show="!search || $el.dataset.permission.includes(search.toLowerCase())">
                                    <input type="checkbox" class="permission-checkbox mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        name="permissions[]" value="{{ $permission->id }}"
                                        @checked(in_array($permission->id, old('permissions', [])))>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-slate-700">{{ \Illuminate\Support\Str::headline(\Illuminate\Support\Str::after($permission->name, '.')) }}</span>
                                        <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $permission->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @empty
                    <x-crud.empty-state icon="key-round" title="No permissions available"
                        description="Create permissions first, then assign them to this role." />
                @endforelse
                <x-input-error :messages="$errors->get('permissions')" />
            </div>
        </x-crud.panel>
        <x-crud.form-actions :cancel-url="route('role.roles.index')"
            submit-label="Create role" loading-label="Creating role" />
    </form>
</div>
@endsection
