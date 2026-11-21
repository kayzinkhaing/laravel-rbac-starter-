@extends('layouts.public')

@section('title', 'Page not found')

@section('content')
<section class="mx-auto flex min-h-[62vh] max-w-xl items-center px-4 py-16 text-center sm:px-6">
    <div class="w-full">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
            <x-icon name="search" class="h-7 w-7" />
        </span>
        <p class="mt-6 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">404 - Not found</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">This page moved or does not exist</h1>
        <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">Check the address or return to the workspace.</p>
        <a href="{{ route('home') }}" class="btn-primary mt-7">Go home</a>
    </div>
</section>
@endsection
