@extends('layouts.app')

@section('title', 'My addresses')
@section('description', 'Manage your saved delivery and contact addresses.')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    <div class="flex justify-end">
        <a href="{{ route('addresses.create') }}" class="btn-primary">Add address</a>
    </div>
    @forelse ($addresses as $address)
        <section class="surface-card flex flex-wrap items-start justify-between gap-4 p-5">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-semibold text-slate-900">{{ $address->address_line_1 }}</h2>
                    @if ($address->is_default)<span class="rounded-full bg-indigo-50 px-2 py-1 text-xs text-indigo-700">Default</span>@endif
                </div>
                <p class="mt-1 text-sm text-slate-600">
                    {{ collect([$address->ward?->name, $address->township?->name, $address->district?->name, $address->state?->name, $address->country?->name])->filter()->join(', ') }}
                </p>
                @if ($address->address_line_2)<p class="text-sm text-slate-500">{{ $address->address_line_2 }}</p>@endif
            </div>
            <div class="flex items-center gap-3">
                <a class="text-sm font-medium text-indigo-600" href="{{ route('addresses.edit', $address) }}">Edit</a>
                <form method="POST" action="{{ route('addresses.destroy', $address) }}" onsubmit="return confirm('Delete this address?')">
                    @csrf @method('DELETE')
                    <button class="text-sm font-medium text-rose-600">Delete</button>
                </form>
            </div>
        </section>
    @empty
        <div class="surface-card p-8 text-center text-sm text-slate-500">You have not saved an address yet.</div>
    @endforelse
    {{ $addresses->links() }}
</div>
@endsection
