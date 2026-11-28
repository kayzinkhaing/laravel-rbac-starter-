@extends('layouts.app')

@section('title', 'Create Master Data Item')
@section('description', 'Add a selectable value to a master data category.')

@section('content')
<div class="mx-auto max-w-3xl">
    <x-crud.page-heading
        title="Create item"
        description="Add a value that can be reused throughout the application."
        :back-url="route('master-data-items.index')"
        back-label="Back to items"
    />
    <x-crud.panel>
        <form action="{{ route('master-data-items.store') }}" method="POST" class="space-y-5">
            @csrf
            @include('master-data-items._form')
            <x-crud.form-actions
                :cancel-url="route('master-data-items.index')"
                submit-label="Create item"
                loading-label="Creating item"
            />
        </form>
    </x-crud.panel>
</div>
@endsection
