@props(['loading' => 'Saving...'])

<button type="submit" {{ $attributes->merge(['class' => 'btn-primary']) }} :disabled="submitting" :aria-busy="submitting">
    <span x-show="!submitting">{{ $slot }}</span>
    <span x-cloak x-show="submitting" class="inline-flex items-center gap-2" role="status">
        <span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-r-transparent"></span>
        {{ $loading }}
    </span>
</button>
