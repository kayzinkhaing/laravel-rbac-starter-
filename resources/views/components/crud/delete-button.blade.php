@props([
    'action',
    'title' => 'Delete this record?',
    'message' => 'This action cannot be undone.',
    'confirmText' => 'Delete',
    'buttonLabel' => '',
    'buttonClass' => 'icon-button text-slate-400 hover:bg-rose-50 hover:text-rose-600',
    'ariaLabel' => 'Delete record',
    'icon' => 'trash-2',
])

@php
    $modalName = 'confirm-delete-' . (string) \Illuminate\Support\Str::uuid();
@endphp

<button
    type="button"
    {{ $attributes->merge([
        'class' => $buttonClass,
        'aria-label' => $ariaLabel,
        'aria-haspopup' => 'dialog',
        'x-on:click' => "\$dispatch('open-modal', '{$modalName}')",
    ]) }}
>
    @if ($icon)
        <x-icon :name="$icon" class="h-4 w-4" />
    @endif
    @if ($buttonLabel !== '')
        <span>{{ $buttonLabel }}</span>
    @endif
</button>

<x-modal :name="$modalName" maxWidth="md">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                <x-icon name="alert-triangle" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $message }}</p>
            </div>
        </div>

        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <x-secondary-button
                type="button"
                x-on:click="$dispatch('close-modal', '{{ $modalName }}')"
            >
                Cancel
            </x-secondary-button>

            <form method="POST" action="{{ $action }}">
                @csrf
                @method('DELETE')
                <x-danger-button type="submit" class="w-full justify-center sm:w-auto">
                    {{ $confirmText }}
                </x-danger-button>
            </form>
        </div>
    </div>
</x-modal>
