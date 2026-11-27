@extends('layouts.app')
@section('title', 'View Master Data Category')
@section('description', 'Category details and management.')
@section('content')
    @php
        $category = $data;
    @endphp
    <section class="mx-auto max-w-4xl space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><a href="{{ route('master-data-categories.index') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700">← Back to categories</a>
                <h2 class="mt-3 text-xl font-semibold text-slate-900">Category details</h2>
                <p class="mt-1 text-sm text-slate-500">View and update this master data category.</p>
            </div><span
                class="rounded-full px-3 py-1 text-xs font-semibold {{ data_get($category, 'is_active') ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ data_get($category, 'is_active') ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="surface-card p-6">
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ data_get($category, 'name', '—') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Key</dt>
                    <dd class="mt-1 font-mono text-sm text-slate-800">{{ data_get($category, 'key', '—') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Sort order</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ data_get($category, 'sort_order', 0) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Created</dt>
                    <dd class="mt-1 text-sm text-slate-800">
                        {{ data_get($category, 'created_at') ? \Illuminate\Support\Carbon::parse(data_get($category, 'created_at'))->format('M j, Y H:i') : '—' }}
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Description</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ data_get($category, 'description') ?: 'No description provided.' }}</dd>
                </div>
            </dl>
        </div>
        @can('manage master data')
            <div class="surface-card p-6">
                <h3 class="text-base font-semibold text-slate-900">Edit category</h3>
                <form method="POST" action="{{ route('master-data-categories.update', data_get($category, 'id')) }}"
                    class="mt-4 space-y-4">@csrf @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="mb-1 block text-sm font-medium text-slate-700">Name *</label><input
                                class="input-control" name="name" required maxlength="150"
                                value="{{ old('name', data_get($category, 'name')) }}"></div>
                        <div><label class="mb-1 block text-sm font-medium text-slate-700">Key *</label><input
                                class="input-control font-mono" name="key" required maxlength="100"
                                value="{{ old('key', data_get($category, 'key')) }}"></div>
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <textarea class="input-control" name="description" rows="3" maxlength="2000">{{ old('description', data_get($category, 'description')) }}</textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div><label class="mb-1 block text-sm font-medium text-slate-700">Sort order</label><input
                                class="input-control" type="number" min="0" name="sort_order"
                                value="{{ old('sort_order', data_get($category, 'sort_order', 0)) }}"></div><label
                            class="flex items-center gap-2 pt-7 text-sm font-medium text-slate-700"><input type="checkbox"
                                name="is_active" value="0"><input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', data_get($category, 'is_active')) ? 'checked' : '' }}> Active
                            category</label>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2"><button class="btn-primary" type="submit">Save
                            changes</button></div>
                </form>
            </div>
            <form method="POST" action="{{ route('master-data-categories.destroy', data_get($category, 'id')) }}"
                onsubmit="return confirm('Are you sure you want to delete this category?')">@csrf @method('DELETE')<button
                    class="btn-secondary text-rose-600 hover:bg-rose-50" type="submit">Delete category</button></form>
        @endcan
    </section>
@endsection
