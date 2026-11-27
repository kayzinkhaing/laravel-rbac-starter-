@extends('layouts.app')
@section('title', 'Edit address')
@section('description', 'Update your saved address.')
@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('addresses.update', $address) }}" class="space-y-5">
        @method('PUT')
        @include('addresses._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('addresses.index') }}" class="btn-secondary">Cancel</a>
            <button class="btn-primary">Update address</button>
        </div>
    </form>
</div>
@endsection
