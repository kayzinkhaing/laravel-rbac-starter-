@props([
    'cancelUrl',
    'submitLabel' => 'Save changes',
    'loadingLabel' => null,
])
<div {{ $attributes->class(['mt-6 flex flex-wrap justify-end gap-3']) }}>
    <a href="{{ $cancelUrl }}" class="btn-secondary">Cancel</a>
    <x-loading-button :loading="$loadingLabel">{{ $submitLabel }}</x-loading-button>
</div>
