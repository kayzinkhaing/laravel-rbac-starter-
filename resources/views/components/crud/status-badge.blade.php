@props(['active' => false, 'activeLabel' => 'Active', 'inactiveLabel' => 'Inactive'])
@if ($active)
    <span {{ $attributes->class(['inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700']) }}>
        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>{{ $activeLabel }}
    </span>
@else
    <span {{ $attributes->class(['inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600']) }}>
        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-slate-400"></span>{{ $inactiveLabel }}
    </span>
@endif
