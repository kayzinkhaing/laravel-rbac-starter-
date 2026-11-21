@extends('layouts.public')

@section('title', 'Access denied')

@section('content')
<section class="mx-auto flex min-h-[62vh] max-w-xl items-center px-4 py-16 text-center sm:px-6">
    <div class="w-full">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
            <x-icon name="shield" class="h-7 w-7" />
        </span>
        <p class="mt-6 text-xs font-semibold uppercase tracking-[0.18em] text-rose-600">403 - Access denied</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">You do not have permission</h1>
        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">
            This page requires a role or permission that is not assigned to your account.
        </p>
        <div class="mt-7 flex justify-center gap-3">
            <a href="{{ route('home') }}" class="btn-secondary">Back to home</a>
            @auth
                @can('access-admin-dashboard')
                    <a href="{{ route('dashboard') }}" class="btn-primary">Open workspace</a>
                @endcan
            @endauth
        </div>
    </div>
</section>
@endsection
