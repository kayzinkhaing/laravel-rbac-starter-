@extends('layouts.app')

@section('title', 'Edit Master Data Item')
@section('description', 'Update this master data value.')

@section('content')
<div class="mx-auto max-w-3xl">
    <x-crud.page-heading
        title="Edit item"
        description="Update the value and its category details."
        :back-url="route('master-data-items.index')"
        back-label="Back to items"
    />
    <x-crud.panel>
        <form action="{{ route('master-data-items.update', $data->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            @include('master-data-items._form', ['data' => $data])
            <x-crud.form-actions
                :cancel-url="route('master-data-items.index')"
                submit-label="Save changes"
                loading-label="Saving changes"
            />
        </form>
    </x-crud.panel>
</div>
@endsection
