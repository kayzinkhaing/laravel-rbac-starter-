<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Account recovery</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ __('Reset your password') }}</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('Enter the email address associated with your account and we will send you a reset link.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" data-recaptcha-action="forgot_password">
        @csrf
        <input type="hidden" name="recaptcha_token" value="">

        <div class="mb-5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <x-primary-button class="w-full">{{ __('Send reset link') }}</x-primary-button>
        <x-input-error :messages="$errors->get('recaptcha_token')" class="mt-3" />
    </form>
    <p class="mt-6 text-center text-sm text-slate-500"><a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Back to sign in</a></p>
</x-guest-layout>
