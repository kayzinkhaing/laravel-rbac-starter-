@extends('layouts.app')

@section('title', 'Payment')

@section('content')
<section class="mx-auto max-w-2xl">
    <div class="surface-card p-5 sm:p-6">
        <h1 class="text-lg font-semibold text-slate-900">Choose a payment provider</h1>
        <p class="mt-1 text-sm text-slate-500">Continue to a configured external checkout.</p>

        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('payment.process') }}" method="POST" class="mt-6">
            @csrf
            <fieldset class="grid gap-3 sm:grid-cols-2">
                <label class="cursor-pointer rounded-xl border border-slate-200 p-4 hover:border-indigo-300">
                    <input type="radio" name="gateway" value="paypal" class="mr-2 text-indigo-600" checked>
                    <span class="font-medium text-slate-900">PayPal</span>
                    <span class="mt-1 block text-xs text-slate-500">Redirect to PayPal</span>
                </label>
                <label class="cursor-pointer rounded-xl border border-slate-200 p-4 hover:border-indigo-300">
                    <input type="radio" name="gateway" value="stripe" class="mr-2 text-indigo-600">
                    <span class="font-medium text-slate-900">Stripe</span>
                    <span class="mt-1 block text-xs text-slate-500">Use Stripe checkout</span>
                </label>
            </fieldset>
            <button type="submit" class="btn-primary mt-5">Continue</button>
        </form>
    </div>
</section>
@endsection
