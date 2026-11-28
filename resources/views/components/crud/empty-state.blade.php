@props([
    'icon' => 'inbox',
    'title' => 'Nothing here yet',
    'description' => null,
])
<div {{ $attributes->class(['px-6 py-14 text-center']) }}>
    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
    @endif
    <div class="mt-5">{{ $slot }}</div>
</div>
