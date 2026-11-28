@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'field-label']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="ml-1 text-rose-600" aria-hidden="true">*</span>
        <span class="sr-only">(required)</span>
    @endif
</label>
