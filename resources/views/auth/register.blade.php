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
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
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
