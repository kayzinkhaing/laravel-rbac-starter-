@props(['name'])

<svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.7', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('layers')
            <path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/>
            @break
        @case('layout-dashboard')
            <rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/>
            @break
        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            @break
        @case('shield')
            <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('key-round')
            <circle cx="8" cy="15" r="5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/>
            @break
        @case('user-round')
            <circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/>
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6"/>
            @break
        @case('panel-left')
            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/>
            @break
        @case('log-out')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>
            @break
        @case('check-circle')
            <circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('arrow-up-right')
            <path d="M7 17 17 7M7 7h10v10"/>
            @break
        @case('more-horizontal')
            <circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>
            @break
        @case('pencil')
            <path d="m18 2 4 4L8 20l-5 1 1-5L18 2Z"/><path d="m14.5 5.5 4 4"/>
            @break
        @case('trash-2')
            <path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/>
            @break
        @case('clock-3')
            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            @break
        @case('users-round')
            <path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 21a8 8 0 0 0-6-7.75M16 3.13a5 5 0 0 1 0 9.75"/>
            @break
        @case('activity')
            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
            @break
        @case('arrow-right')
            <path d="M5 12h14M12 5l7 7-7 7"/>
            @break
        @case('arrow-left')
            <path d="M19 12H5M12 19l-7-7 7-7"/>
            @break
        @case('filter')
            <path d="M4 7h16M7 12h10m-7 5h4"/>
            @break
        @case('check')
            <path d="m5 12 4 4L19 6"/>
            @break
        @case('x')
            <path d="m18 6-12 12M6 6l12 12"/>
            @break
        @default
            <circle cx="12" cy="12" r="9"/>
    @endswitch
</svg>
