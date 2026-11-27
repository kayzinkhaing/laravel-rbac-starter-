@extends('layouts.app')

@section('title', 'Create Master Data Category')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Create Category</h1>
        <a href="{{ route('master-data-categories.index') }}" class="btn-secondary">Back</a>
    </div>

    <div class="surface-card p-6">
        <form action="{{ route('master-data-categories.store') }}" method="POST">
            @csrf
            @include('master-data-categories._form')

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master-data-categories.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
