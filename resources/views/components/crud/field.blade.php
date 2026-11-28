@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'isRequired' => false,
    'hint' => null,
    'rows' => 4,
])

<div {{ $attributes->class(['space-y-1.5']) }}>
    <label for="{{ $name }}" class="field-label">
        {{ $label }}
        @if ($isRequired)
            <span class="ml-1 text-rose-600" aria-hidden="true">*</span>
            <span class="sr-only">(required)</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            class="input-control"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            {{ $attributes->except(['class', 'required']) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            class="input-control"
            value="{{ old($name, $value) }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            {{ $attributes->except(['class', 'required']) }}
        >
    @endif

    @if ($hint)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($name)" />
</div>
