@props(['padding' => true])
<section {{ $attributes->class(['surface-card', 'p-5 sm:p-6' => $padding, 'overflow-hidden' => ! $padding]) }}>
    {{ $slot }}
</section>
