@extends('layouts.app')

@section('title', 'Payment complete')

@section('content')
<section class="mx-auto flex max-w-xl flex-col items-center py-16 text-center">
    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><x-icon name="check-circle" class="h-7 w-7" /></span>
    <h1 class="mt-5 text-2xl font-semibold text-slate-900">Payment complete</h1>
    <p class="mt-2 text-sm text-slate-500">The payment flow completed successfully.</p>
    <a href="{{ route('home') }}" class="btn-secondary mt-6">Return home</a>
</section>
@endsection