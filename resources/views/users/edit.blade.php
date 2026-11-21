@extends('layouts.app')

@section('title', 'Edit user')
@section('description', 'Update account details and access.')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('role.users.show', ['user' => $user]) }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="h-4 w-4" /> Back to user</a>
    <form action="{{ route('role.users.update', ['user' => $user]) }}" method="POST" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
        @include('users._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('role.users.show', ['user' => $user]) }}" class="btn-secondary">Cancel</a>
            <x-loading-button>Save changes</x-loading-button>
        </div>
    </form>
</div>
@endsection
