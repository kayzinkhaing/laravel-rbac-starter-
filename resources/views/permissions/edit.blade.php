@extends('layouts.app')

@section('title', 'Edit permission')
@section('description', 'Update this access rule.')

@section('content')
<div class="mx-auto max-w-2xl">
    <x-crud.page-heading title="Edit permission"
        description="Update the access rule used by roles."
        :back-url="route('role.permissions.index')" back-label="Back to permissions" />
    <x-crud.panel>
        <form action="{{ route('role.permissions.update', ['permission' => $permission]) }}" method="POST">
            @csrf
            @method('PUT')
            <h2 class="text-sm font-semibold text-slate-900">Permission details</h2>
            <p class="mt-1 text-sm text-slate-500">Renaming this permission updates the access rule assigned to roles.</p>
            <div class="mt-5">
                <x-crud.field name="name" label="Permission name" :value="$permission->name"
                    maxlength="255" is-required autofocus />
            </div>
            <x-crud.form-actions :cancel-url="route('role.permissions.index')"
                submit-label="Save changes" loading-label="Saving changes" />
        </form>
    </x-crud.panel>
</div>
@endsection
