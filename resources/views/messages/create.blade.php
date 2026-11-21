@extends('layouts.app')

@section('title', 'Create message')

@section('content')
<section class="mx-auto max-w-2xl">
    <a href="{{ route('messages.index') }}" class="mb-5 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="h-4 w-4" /> Back to messages</a>
    <div class="surface-card p-5 sm:p-6">
        <h2 class="text-lg font-semibold text-slate-900">Create message</h2>
        <form action="{{ route('messages.store') }}" method="POST" class="mt-5 space-y-5">
            @csrf
            <div>
                <label for="name" class="field-label">Key</label>
                <input type="text" name="name" id="name" class="input-control" value="{{ old('name') }}" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="message" class="field-label">Value</label>
                <textarea name="message" id="message" rows="5" class="input-control" required>{{ old('message') }}</textarea>
                <x-input-error :messages="$errors->get('message')" class="mt-2" />
            </div>
            <button type="submit" class="btn-primary">Create message</button>
        </form>
    </div>
</section>
@endsection