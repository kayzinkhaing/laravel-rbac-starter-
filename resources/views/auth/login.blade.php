<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Sign in</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Welcome back</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Enter your details to access your workspace.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->has('oauth'))
        <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-3 text-sm text-rose-800">
            {{ $errors->first('oauth') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" data-recaptcha-action="login">
        @csrf
        <input type="hidden" name="recaptcha_token" value="">

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-600 hover:text-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>
        </div>

        <div>
            <x-primary-button class="w-full">
                {{ __('Sign in') }}
            </x-primary-button>
        </div>
        <x-input-error :messages="$errors->get('recaptcha_token')" class="mt-3" />
    </form>

    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-slate-200"></span>
        <span class="text-xs font-medium uppercase tracking-[0.12em] text-slate-400">or continue with</span>
        <span class="h-px flex-1 bg-slate-200"></span>
    </div>

    <div class="grid grid-cols-2 gap-3">
        {{-- Google Login --}}
        <a href="{{ route('google') }}"
            class="btn-secondary flex min-h-[2.75rem] items-center justify-center gap-2 px-3">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335"
                    d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5Z" />
                <path fill="#4285F4"
                    d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.76 7.18l7.73 6C44.42 37.98 46.98 31.86 46.98 24.55Z" />
                <path fill="#FBBC05"
                    d="M10.53 28.59a14.4 14.4 0 0 1 0-9.18l-7.98-6.19a24 24 0 0 0 0 21.56l7.98-6.19Z" />
                <path fill="#34A853"
                    d="M24 48c6.48 0 11.93-2.13 15.91-5.8l-7.73-6c-2.14 1.45-4.88 2.3-8.18 2.3-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48Z" />
            </svg>
            <span>Google</span>
        </a>

        {{-- GitHub Login --}}
        <a href="{{ route('github') }}"
            class="btn-secondary flex min-h-[2.75rem] items-center justify-center gap-2 px-3">
            <svg class="h-5 w-5 shrink-0 fill-slate-900 dark:fill-white" viewBox="0 0 24 24" aria-hidden="true">
                <path
                    d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.55.1.76-.24.76-.54v-2.08c-3.1.67-3.75-1.32-3.75-1.32-.5-1.29-1.23-1.63-1.23-1.63-1.01-.69.08-.68.08-.68 1.12.08 1.7 1.15 1.7 1.15.99 1.69 2.6 1.2 3.23.92.1-.72.39-1.2.7-1.48-2.47-.28-5.07-1.24-5.07-5.5 0-1.22.44-2.22 1.15-3-.12-.28-.5-1.42.11-2.96 0 0 .94-.3 3.05 1.14a10.6 10.6 0 0 1 5.55 0c2.12-1.44 3.05-1.14 3.05-1.14.61 1.54.23 2.68.12 2.96.72.78 1.14 1.78 1.14 3.01 0 4.27-2.6 5.21-5.08 5.49.4.35.75 1.02.75 2.06v3.06c0 .3.2.65.77.54A11.1 11.1 0 0 0 12 .9Z" />
            </svg>
            <span>GitHub</span>
        </a>
    </div>


    @if (Route::has('register'))
        <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Create an
                account</a>
        </p>
    @endif
</x-guest-layout>
