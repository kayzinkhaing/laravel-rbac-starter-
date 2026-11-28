<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Workspace') }} - Account</title>
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
    <button type="button" class="icon-button fixed right-4 top-4 z-50 bg-white shadow-sm"
        x-data="{ dark: document.documentElement.dataset.theme === 'dark' }"
        @click="dark = !dark; document.documentElement.dataset.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
        :aria-label="dark ? 'Switch to light theme' : 'Switch to dark theme'">
        <span x-text="dark ? '☀' : '◐'" aria-hidden="true"></span>
    </button>
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(24rem,0.82fr)]">
        <section class="relative hidden overflow-hidden bg-slate-950 p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-14">
            <div class="absolute -right-36 -top-32 h-[28rem] w-[28rem] rounded-full border border-white/[0.06]"></div>
            <div class="absolute -right-20 -top-16 h-[22rem] w-[22rem] rounded-full border border-white/[0.08]"></div>
            <a href="{{ route('home') }}" class="relative z-10 inline-flex w-fit items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500">
                    <x-icon name="layers" class="h-5 w-5" />
                </span>
                <span class="text-sm font-semibold tracking-wide">{{ config('app.name', 'Workspace') }}</span>
            </a>
            <div class="relative z-10 max-w-xl">
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">Administration</p>
                <h1 class="text-4xl font-semibold leading-tight tracking-tight xl:text-5xl">Everything in order.<br>Room to move faster.</h1>
                <p class="mt-5 max-w-md text-base leading-7 text-slate-400">A clear, focused workspace for managing access and keeping your team moving.</p>
            </div>
            <p class="relative z-10 text-xs text-slate-500">Secure access to your workspace</p>
        </section>

        <section class="flex min-h-screen flex-col items-center justify-center px-5 py-10 sm:px-8">
            <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2.5 lg:hidden">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
                    <x-icon name="layers" class="h-[18px] w-[18px]" />
                </span>
                <span class="text-sm font-semibold">{{ config('app.name', 'Workspace') }}</span>
            </a>
            <div class="w-full max-w-[25rem]">
                {{ $slot }}
            </div>
            <p class="mt-8 text-center text-xs text-slate-400">Protected access - {{ config('app.name', 'Workspace') }}</p>
        </section>
    </main>
    @if (config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
        <script>
            document.addEventListener('submit', async function (event) {
                const form = event.target.closest('form[data-recaptcha-action]');
                if (!form) return;

                event.preventDefault();
                const button = form.querySelector('[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');
                }

                try {
                    const token = await grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                        action: form.dataset.recaptchaAction
                    });
                    const input = form.querySelector('[name="recaptcha_token"]');
                    if (input) input.value = token;
                    form.submit();
                } catch (error) {
                    if (button) {
                        button.disabled = false;
                        button.removeAttribute('aria-busy');
                    }
                    alert('Security verification could not start. Please try again.');
                }
            }, true);
        </script>
    @endif
</body>
</html>
