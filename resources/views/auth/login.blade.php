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
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-600 hover:text-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
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
        <a href="{{ route('google') }}" class="btn-secondary min-h-[2.75rem] px-3">Google</a>
        <a href="{{ route('github') }}" class="btn-secondary min-h-[2.75rem] px-3">GitHub</a>
    </div>

    @if (Route::has('register'))
        <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Create an account</a>
        </p>
    @endif
</x-guest-layout>
