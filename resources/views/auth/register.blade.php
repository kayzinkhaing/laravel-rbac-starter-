<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Get started</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Create your account</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Set up your details to join the workspace.</p>
    </div>
    <form method="POST" action="{{ route('register') }}" data-recaptcha-action="register">
        @csrf
        <input type="hidden" name="recaptcha_token" value="">

        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" :required="true" />
            <x-text-input id="name" class="mt-1 w-full" type="text" name="name" :value="old('name')" autofocus autocomplete="name" placeholder="Your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" :required="true" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" :required="true" />
            <div x-data="{ showPassword: false }" class="mt-1" style="position: relative;">
            <x-text-input id="password" class="w-full pr-12" x-bind:type="showPassword ? 'text' : 'password'" name="password" autocomplete="new-password" placeholder="At least 8 characters" />

    <button
        type="button"
        @click="showPassword = !showPassword"
        aria-label="Toggle password visibility"
        title="Show or hide password"
        class="text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); z-index: 10;"
    >
        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>

        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <path d="m3 3 18 18"/>
            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
            <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.9"/>
            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.6"/>
        </svg>
    </button>

        </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" :required="true" />
            <div x-data="{ showPassword: false }" class="mt-1" style="position: relative;">
            <x-text-input id="password_confirmation" class="w-full pr-12" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" placeholder="Re-enter your password" />

    <button
        type="button"
        @click="showPassword = !showPassword"
        aria-label="Toggle password visibility"
        title="Show or hide password"
        class="text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); z-index: 10;"
    >
        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>

        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round"
            aria-hidden="true">
            <path d="m3 3 18 18"/>
            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
            <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.9"/>
            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.6"/>
        </svg>
    </button>

        </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

                <div class="mb-6">
            <label for="terms" class="flex cursor-pointer items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input
                    id="terms"
                    type="checkbox"
                    name="terms"
                    value="1"
                    @checked(old('terms'))
                    class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                >
                <span>
                    I agree to the
                    <span class="font-medium text-indigo-600">Terms and Conditions</span>.
                    <span class="text-rose-600">*</span>
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
                {{ __('Register') }}
        </x-primary-button>
        <x-input-error :messages="$errors->get('recaptcha_token')" class="mt-3" />
    </form>
    <p class="mt-6 border-t border-slate-100 pt-5 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-700">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
