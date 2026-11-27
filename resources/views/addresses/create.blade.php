@extends('layouts.app')
@section('title', 'Add address')
@section('description', 'Save a delivery or contact address.')
@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('addresses.store') }}" class="space-y-5">
        @include('addresses._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('addresses.index') }}" class="btn-secondary">Cancel</a>
            <button class="btn-primary">Save address</button>
        </div>
    </form>
</div>
@endsection
