@extends('layouts.app')

@section('title', 'Create permission')
@section('description', 'Add a specific access rule to the catalog.')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('role.permissions.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="h-4 w-4" /> Back to permissions</a>
    <form action="{{ route('role.permissions.store') }}" method="POST" class="surface-card p-5 sm:p-6" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <h2 class="text-sm font-semibold text-slate-900">Permission details</h2>
        <p class="mt-1 text-sm text-slate-500">Use a module.action name, such as <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">users.view</code>.</p>
        <div class="mt-5">
            <label for="name" class="field-label">Permission name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" class="input-control" placeholder="module.action" required autofocus>
            <x-input-error :messages="$errors->get('name')" />
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('role.permissions.index') }}" class="btn-secondary">Cancel</a>
            <x-loading-button loading="Creating permission">Create permission</x-loading-button>
        </div>
    </form>
</div>
@endsection
