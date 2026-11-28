@props([
    'title',
    'description' => null,
    'backUrl' => null,
    'backLabel' => 'Back',
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => 'plus',
])
<section {{ $attributes->class(['mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center']) }}>
    <div class="min-w-0">
        <h2 class="text-lg font-semibold tracking-tight text-slate-900">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>
    <div class="flex shrink-0 flex-wrap items-center gap-2">
        @if ($backUrl)
            <a href="{{ $backUrl }}" class="btn-secondary">
                <x-icon name="arrow-left" class="h-4 w-4" /> {{ $backLabel }}
            </a>
        @endif
        @if ($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="btn-primary">
                <x-icon :name="$actionIcon" class="h-4 w-4" /> {{ $actionLabel }}
            </a>
        @endif
        {{ $slot }}
    </div>
</section>
