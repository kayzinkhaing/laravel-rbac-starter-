@extends('layouts.app')

@section('content')
<section class="mx-auto flex max-w-xl flex-col items-center py-16 text-center">
    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><x-icon name="x" class="h-7 w-7" /></span>
    <h1 class="mt-5 text-2xl font-semibold text-slate-900">Payment canceled</h1>
    <p class="mt-2 text-sm text-slate-500">No payment was completed. You can choose a provider again.</p>
    <a href="{{ route('payment.select') }}" class="btn-primary mt-6">Try again</a>
</section>
@endsection