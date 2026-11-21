<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') - {{ config('app.name', 'Access Management') }}</title>
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
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <nav x-data="{ profileOpen: false }" class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center gap-2.5 rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
                        <x-icon name="layers" class="h-[18px] w-[18px]" />
                    </span>
                    <span class="sr-only text-sm font-semibold tracking-tight text-slate-900 sm:not-sr-only sm:inline">{{ config('app.name', 'Access Management') }}</span>
                </a>

                <div class="flex items-center gap-2 sm:gap-4">
                    <button type="button" class="icon-button" x-data="{ dark: document.documentElement.dataset.theme === 'dark' }"
                        @click="dark = !dark; document.documentElement.dataset.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                        :aria-label="dark ? 'Switch to light theme' : 'Switch to dark theme'">
                        <span x-text="dark ? '☀' : '◐'" aria-hidden="true"></span>
                    </button>
                    <a href="{{ route('home') }}" @class([
                        'rounded-md px-2.5 py-2 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
                        'text-indigo-700' => request()->routeIs('home'),
                        'text-slate-600 hover:text-slate-900' => !request()->routeIs('home'),
                    ])>Home</a>

                    @guest
                        <a href="{{ route('login') }}" class="rounded-md px-2.5 py-2 text-sm font-medium text-slate-600 transition-colors hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">Sign in</a>
                        <a href="{{ route('register') }}" class="btn-primary min-h-[2.25rem] px-3 text-xs sm:px-4 sm:text-sm">Create account</a>
                    @else
                        <div class="relative" @click.outside="profileOpen = false">
                            <button
                                type="button"
                                @click="profileOpen = !profileOpen"
                                :aria-expanded="profileOpen"
                                aria-haspopup="true"
                                class="inline-flex items-center gap-2 rounded-lg p-1.5 text-left transition-colors hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            >
                                @if (auth()->user()->profileImage())
                                    <img src="{{ route('profile.image', ['user' => auth()->user()]) }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-white">
                                @else
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-xs font-semibold text-indigo-700">
                                        {{ strtoupper(substr(auth()->user()->name ?: auth()->user()->email, 0, 1)) }}
                                    </span>
                                @endif
                                <span class="hidden max-w-36 truncate text-sm font-medium text-slate-700 sm:block">{{ auth()->user()->name }}</span>
                                <x-icon name="chevron-down" class="h-4 w-4 text-slate-400" />
                            </button>
                            <div
                                x-cloak
                                x-show="profileOpen"
                                x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg shadow-slate-900/5"
                            >
                                <div class="border-b border-slate-100 px-3 py-2">
                                    <p class="truncate text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="menu-link">
                                    <x-icon name="user-round" class="h-4 w-4" /> My profile
                                </a>
                                @can('access-admin-dashboard')
                                    <a href="{{ route('dashboard') }}" class="menu-link">
                                        <x-icon name="layout-dashboard" class="h-4 w-4" /> Admin workspace
                                    </a>
                                @endcan
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="menu-link w-full text-left">
                                        <x-icon name="log-out" class="h-4 w-4" /> Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </nav>
        </header>

        <main class="flex-1">
            @if (session('success'))
                <div role="status" class="mx-auto mt-5 flex max-w-7xl items-start gap-3 px-4 sm:px-6 lg:px-8">
                    <p class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('status') === 'profile-updated')
                <div role="status" class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Your profile was updated.</p>
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-slate-50">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="font-medium text-slate-700">{{ config('app.name', 'Access Management') }}</p>
                <p>User management and role-based access control.</p>
                <p>&copy; {{ now()->year }} {{ config('app.name', 'Access Management') }}</p>
            </div>
        </footer>
    </div>
</body>
</html>
