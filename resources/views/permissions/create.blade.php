@extends('layouts.app')

@section('title', 'Create permission')
@section('description', 'Add a specific access rule to the catalog.')

@section('content')
<div class="mx-auto max-w-2xl">
    <x-crud.page-heading title="Create permission"
        description="Add a new access rule that can be assigned to roles."
        :back-url="route('role.permissions.index')" back-label="Back to permissions" />
    <x-crud.panel>
        <form action="{{ route('role.permissions.store') }}" method="POST">
            @csrf
            <h2 class="text-sm font-semibold text-slate-900">Permission details</h2>
            <p class="mt-1 text-sm text-slate-500">Use a module.action name, such as <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">users.view</code>.</p>
            <div class="mt-5">
                <x-crud.field name="name" label="Permission name" :value="old('name')"
                    placeholder="module.action" maxlength="255" is-required autofocus />
            </div>
            <x-crud.form-actions :cancel-url="route('role.permissions.index')"
                submit-label="Create permission" loading-label="Creating permission" />
        </form>
    </x-crud.panel>
</div>
@endsection
