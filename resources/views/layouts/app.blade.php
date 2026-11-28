<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Workspace')) - {{ config('app.name', 'Workspace') }}</title>
    <script>
        (function () {
            const dark = localStorage.getItem('theme') === 'dark';
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            document.documentElement.classList.toggle('dark', dark);
        }());
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('rbac-logo.png') }}?v=1">
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div
        x-data="{ sidebarOpen: false, sidebarCollapsed: false, userMenuOpen: false }"
        x-init="sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true'"
        @keydown.escape.window="sidebarOpen = false; userMenuOpen = false"
        class="min-h-screen"
    >
        <a href="#main-content" class="sr-only z-50 rounded-md bg-white px-3 py-2 text-sm font-medium text-indigo-700 focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:ring-2 focus:ring-indigo-500">
            Skip to content
        </a>
        @include('layouts.navigation')

        <div class="min-h-screen transition-[padding] duration-200 lg:pl-64" :class="{ 'lg:pl-[5.5rem]': sidebarCollapsed }">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur">
                <div class="flex min-h-[4.5rem] items-center gap-4 px-4 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        @click="if (window.innerWidth < 1024) { sidebarOpen = !sidebarOpen } else { sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar-collapsed', sidebarCollapsed) }"
                        class="icon-button"
                        aria-label="Toggle navigation"
                        :aria-expanded="window.matchMedia('(min-width: 1024px)').matches ? !sidebarCollapsed : sidebarOpen"
                    >
                        <x-icon name="panel-left" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0 flex-1">
                        <nav aria-label="Breadcrumb" class="hidden text-xs font-medium text-slate-500 sm:block">
                            @if ($activeRole ?? false)
                                <a href="{{ route('role.dashboard', ['roleSlug' => $roleSlugs[$activeRole->id]]) }}" class="hover:text-slate-800">{{ \Illuminate\Support\Str::headline($activeRole->name) }}</a>
                            @elseif (auth()->user()->can('access-admin-dashboard'))
                                <a href="{{ route('dashboard') }}" class="hover:text-slate-800">Admin workspace</a>
                            @else
                                <a href="{{ route('home') }}" class="hover:text-slate-800">Home</a>
                            @endcan
                            <span class="mx-1.5 text-slate-300">/</span>
                            <span class="text-slate-700">@yield('title', 'Dashboard')</span>
                        </nav>
                        <div class="flex items-center gap-3">
                            @hasSection('title')
                                <h1 class="truncate text-base font-semibold tracking-tight text-slate-900 sm:text-lg">@yield('title')</h1>
                            @elseif(isset($header))
                                <div class="truncate text-base font-semibold tracking-tight text-slate-900 sm:text-lg">{{ $header }}</div>
                            @else
                                <h1 class="truncate text-base font-semibold tracking-tight text-slate-900 sm:text-lg">Dashboard</h1>
                            @endif
                            @hasSection('description')
                                <span class="hidden h-4 border-l border-slate-200 sm:block"></span>
                                <p class="hidden truncate text-sm text-slate-500 md:block">@yield('description')</p>
                            @endif
                        </div>
                    </div>

                    <button type="button" class="icon-button" x-data="{ dark: document.documentElement.dataset.theme === 'dark' }"
                        @click="dark = !dark; document.documentElement.dataset.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                        :aria-label="dark ? 'Switch to light theme' : 'Switch to dark theme'">
                        <x-icon name="sun" class="h-5 w-5" x-show="dark" />
                        <x-icon name="moon" class="h-5 w-5" x-show="!dark" />
                    </button>

                    <div class="relative">
                        <button
                            type="button"
                            @click="userMenuOpen = !userMenuOpen"
                            @click.outside="userMenuOpen = false"
                            :aria-expanded="userMenuOpen"
                            aria-label="Open profile menu"
                            class="flex items-center gap-2 rounded-lg p-1.5 text-left hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        >
                            @if (auth()->user()->profileImage())
                                <img src="{{ route('profile.image', ['user' => auth()->user()]) }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-white">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">
                                    {{ strtoupper(substr(auth()->user()->name ?: auth()->user()->email, 0, 1)) }}
                                </span>
                            @endif
                            <span class="hidden max-w-36 truncate text-sm font-medium text-slate-700 sm:block">{{ auth()->user()->name }}</span>
                            <x-icon name="chevron-down" class="hidden h-4 w-4 text-slate-400 sm:block" />
                        </button>
                        <div
                            x-cloak
                            x-show="userMenuOpen"
                            x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg shadow-slate-900/5"
                        >
                            <div class="border-b border-slate-100 px-3 py-2">
                                <p class="truncate text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ \App\Support\AdminDashboardAccess::profileUrlFor(auth()->user()) }}" class="menu-link">
                                <x-icon name="user-round" class="h-4 w-4" /> My profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="menu-link w-full text-left">
                                    <x-icon name="log-out" class="h-4 w-4" /> Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main id="main-content" class="mx-auto w-full max-w-[1440px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                @if (session('success'))
                    <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0" />
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert" class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>
        </div>
    </div>
    @yield('scripts')
</body>
</html>