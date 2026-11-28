@extends('layouts.app')

@section('title', 'Edit Master Data Category')
@section('description', 'Update this reusable master data category.')

@section('content')
<div class="mx-auto max-w-3xl">
    <x-crud.page-heading
        title="Edit category"
        description="Update the category details used throughout the application."
        :back-url="route('master-data-categories.index')"
        back-label="Back to categories"
    />
    <x-crud.panel>
        <form action="{{ route('master-data-categories.update', $data->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            @include('master-data-categories._form', ['data' => $data])
            <x-crud.form-actions
                :cancel-url="route('master-data-categories.index')"
                submit-label="Save changes"
                loading-label="Saving changes"
            />
        </form>
    </x-crud.panel>
</div>
@endsection
