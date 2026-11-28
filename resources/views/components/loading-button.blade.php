@props(['loading' => 'Saving...'])

<button
    type="submit"
    {{ $attributes->merge(['class' => 'btn-primary']) }}
>
    <span>{{ $slot }}</span>
</button>
