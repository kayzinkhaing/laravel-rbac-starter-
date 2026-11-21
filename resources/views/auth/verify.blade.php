<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Account security</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Verify your sign-in</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Enter a six-digit authenticator code or one unused recovery code.</p>
    </div>

    <form method="POST" action="{{ route('twofactor.verify.post') }}">
        @csrf

        <div class="mb-6">
            <x-input-label for="code" :value="__('Verification code')" />
            <x-text-input
                id="code"
                class="mt-1 w-full"
                type="text"
                name="code"
                inputmode="numeric"
                maxlength="20"
                autocomplete="one-time-code"
                required
                autofocus
                placeholder="000000"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">{{ __('Verify and continue') }}</x-primary-button>
    </form>
</x-guest-layout>
