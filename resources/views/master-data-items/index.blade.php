@extends('layouts.app')

@section('title', 'Master Data Items')
@section('description', 'Manage selectable values inside each master data category.')

@section('content')
    @php
        $items = $masterdataitems?->getCollection() ?? collect();
        $categories = \App\Models\MasterDataCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'key', 'is_active']);
    @endphp
    <section class="space-y-5">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Master data values</h2>
                <p class="mt-1 text-sm text-slate-500">Manage values such as pending, rejected, and completed.</p>
            </div>
            @can('manage master data')
                <a class="btn-primary" href="{{ route('master-data-items.create') }}"><x-icon name="plus" class="h-4 w-4" /> Add item</a>
            @endcan
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
            <div class="surface-card p-4"><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total items</p><p class="mt-2 text-2xl font-semibold">{{ $items->count() }}</p></div>
            <div class="surface-card p-4"><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Active items</p><p class="mt-2 text-2xl font-semibold text-emerald-600">{{ $items->where('is_active', true)->count() }}</p></div>
            <div class="surface-card p-4"><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Categories</p><p class="mt-2 text-2xl font-semibold text-indigo-600">{{ $categories->count() }}</p></div>
        </div>
        <div class="surface-card overflow-hidden">
            @if ($items->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[58rem] text-left">
                        <thead><tr class="table-heading border-b border-slate-100">
                            <th class="px-5 py-3.5 font-medium sm:px-6">Item</th><th class="px-5 py-3.5 font-medium">Category</th><th class="px-5 py-3.5 font-medium">Key</th><th class="px-5 py-3.5 font-medium">Sort</th><th class="px-5 py-3.5 font-medium">Status</th><th class="px-5 py-3.5 text-right font-medium">Actions</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($items as $item)
                                @php($category = $categories->firstWhere('id', data_get($item, 'master_data_category_id')))
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4 sm:px-6"><p class="text-sm font-semibold text-slate-800">{{ data_get($item, 'label') ?: data_get($item, 'value', '—') }}</p>@if (data_get($item, 'label'))<p class="text-xs text-slate-500">{{ data_get($item, 'value') }}</p>@endif</td>
                                    <td class="px-5 py-4"><p class="text-sm font-medium text-slate-700">{{ $category?->name ?? 'Unknown category' }}</p><p class="text-xs text-slate-400">{{ $category?->key }}</p></td>
                                    <td class="px-5 py-4"><code class="rounded bg-slate-100 px-2 py-1 text-xs">{{ data_get($item, 'key', '—') }}</code></td>
                                    <td class="px-5 py-4 text-sm">{{ data_get($item, 'sort_order', 0) }}</td>
                                    <td class="px-5 py-4">@if (data_get($item, 'is_active'))<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>@else<span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>@endif</td>
                                    <td class="px-5 py-4"><div class="flex items-center justify-end gap-1.5">
                                        <a class="icon-button" href="{{ route('master-data-items.show', data_get($item, 'id')) }}" aria-label="View item"><x-icon name="eye" class="h-4 w-4" /></a>
                                        @can('manage master data')
                                            <a class="icon-button" href="{{ route('master-data-items.edit', data_get($item, 'id')) }}" aria-label="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                            <x-crud.delete-button
                                                :action="route('master-data-items.destroy', data_get($item, 'id'))"
                                                title="Delete item?"
                                                :message="'Are you sure you want to delete ' . (data_get($item, 'label') ?: data_get($item, 'value', 'this item')) . '?'"
                                                :aria-label="'Delete ' . (data_get($item, 'label') ?: data_get($item, 'value', 'item'))"
                                            />
                                        @endcan
                                    </div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-16 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><x-icon name="list-plus" class="h-6 w-6" /></span><h3 class="mt-4 text-sm font-semibold text-slate-900">No items yet</h3><p class="mt-1 text-sm text-slate-500">Create a category first, then add its values.</p>@can('manage master data')<a class="btn-primary mt-5" href="{{ route('master-data-items.create') }}"><x-icon name="plus" class="h-4 w-4" /> Add item</a>@endcan</div>
            @endif
        </div>
    </section>
@endsection
