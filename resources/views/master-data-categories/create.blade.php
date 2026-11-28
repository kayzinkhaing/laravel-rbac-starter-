@extends('layouts.app')

@section('title', 'Create Master Data Category')
@section('description', 'Add a reusable category for application data.')

@section('content')
<div class="mx-auto max-w-3xl">
    <x-crud.page-heading
        title="Create category"
        description="Define a reusable group for related master data values."
        :back-url="route('master-data-categories.index')"
        back-label="Back to categories"
    />
    <x-crud.panel>
        <form action="{{ route('master-data-categories.store') }}" method="POST" class="space-y-5">
            @csrf
            @include('master-data-categories._form')
            <x-crud.form-actions
                :cancel-url="route('master-data-categories.index')"
                submit-label="Create category"
                loading-label="Creating category"
            />
        </form>
    </x-crud.panel>
</div>
@endsection
