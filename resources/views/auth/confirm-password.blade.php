<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Security check</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ __('Confirm your password') }}</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">{{ __('This is a secure area. Please confirm your password before continuing.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-5">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <x-primary-button class="w-full">{{ __('Confirm') }}</x-primary-button>
    </form>
</x-guest-layout>
