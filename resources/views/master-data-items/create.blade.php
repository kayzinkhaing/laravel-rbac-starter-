@extends('layouts.app')

@section('title', 'Create Master Data Item')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Create Item</h1>
        <a href="{{ route('master-data-items.index') }}" class="btn-secondary">Back</a>
    </div>

    <div class="surface-card p-6">
        <form action="{{ route('master-data-items.store') }}" method="POST">
            @csrf
            @include('master-data-items._form')

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master-data-items.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
