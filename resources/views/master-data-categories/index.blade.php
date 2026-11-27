@extends('layouts.app')

@section('title', 'Master Data Categories')
@section('description', 'Organize reusable master data values for your application.')

@section('content')
@php
    $categories = $masterdatacategories?->getCollection() ?? collect();
@endphp

<section class="space-y-5">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-semibold tracking-tight text-slate-900">Category catalog</h2>
            <p class="mt-1 text-sm text-slate-500">Manage reusable groups such as order status, payment status, and delivery type.</p>
        </div>
        @can('manage master data')
            <a class="btn-primary" href="{{ route('master-data-categories.create') }}">
                <x-icon name="plus" class="h-4 w-4" /> Add category
            </a>
        @endcan
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="surface-card p-4"><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total categories</p><p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900">{{ $categories->count() }}</p></div>
        <div class="surface-card p-4"><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Active</p><p class="mt-2 text-2xl font-semibold tabular-nums text-emerald-600">{{ $categories->where('is_active', true)->count() }}</p></div>
        <div class="surface-card p-4"><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Inactive</p><p class="mt-2 text-2xl font-semibold tabular-nums text-slate-500">{{ $categories->where('is_active', false)->count() }}</p></div>
    </div>

    <div class="surface-card overflow-hidden">
        @if ($categories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[54rem] text-left">
                    <thead><tr class="table-heading border-b border-slate-100">
                        <th class="px-5 py-3.5 font-medium sm:px-6">Category</th><th class="px-5 py-3.5 font-medium">Key</th><th class="px-5 py-3.5 font-medium">Sort order</th><th class="px-5 py-3.5 font-medium">Status</th><th class="px-5 py-3.5 text-right font-medium sm:px-6">Actions</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach ($categories as $category)
                        <tr class="transition-colors hover:bg-slate-50/70">
                            <td class="px-5 py-4 sm:px-6"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><x-icon name="folder" class="h-[18px] w-[18px]" /></span><div><p class="text-sm font-semibold text-slate-800">{{ data_get($category, 'name', '—') }}</p><p class="mt-0.5 max-w-sm truncate text-xs text-slate-500">{{ data_get($category, 'description') ?: 'No description' }}</p></div></div></td>
                            <td class="px-5 py-4"><code class="rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ data_get($category, 'key', '—') }}</code></td>
                            <td class="px-5 py-4 text-sm tabular-nums text-slate-600">{{ data_get($category, 'sort_order', 0) }}</td>
                            <td class="px-5 py-4">@if (data_get($category, 'is_active'))<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>@else<span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>@endif</td>
                            <td class="px-5 py-4 sm:px-6"><div class="flex items-center justify-end gap-1.5">
                                <a class="icon-button" href="{{ route('master-data-categories.show', data_get($category, 'id')) }}" aria-label="View category"><x-icon name="eye" class="h-4 w-4" /></a>
                                @can('manage master data')
                                    <a class="icon-button" href="{{ route('master-data-categories.edit', data_get($category, 'id')) }}" aria-label="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                    <form method="POST" action="{{ route('master-data-categories.destroy', data_get($category, 'id')) }}" onsubmit="return confirm('Delete this category? Categories with existing items are protected by the database relationship.')">@csrf @method('DELETE')<button type="submit" class="icon-button text-slate-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Delete category"><x-icon name="trash-2" class="h-4 w-4" /></button></form>
                                @endcan
                            </div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-16 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><x-icon name="folder-plus" class="h-6 w-6" /></span><h3 class="mt-4 text-sm font-semibold text-slate-900">No categories yet</h3><p class="mt-1 text-sm text-slate-500">Create your first category to organize reusable values.</p>@can('manage master data')<a class="btn-primary mt-5" href="{{ route('master-data-categories.create') }}"><x-icon name="plus" class="h-4 w-4" /> Add category</a>@endcan</div>
        @endif
    </div>
</section>

@endsection
