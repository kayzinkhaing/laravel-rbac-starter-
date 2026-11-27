
<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">
            Email verification
        </p>

        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
            Verify your email
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Enter the six-digit code sent to
            <strong>{{ $email }}</strong>.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.otp.verify') }}">
        @csrf

        <div class="mb-5">
            <x-input-label for="otp" value="Verification code" />

            <x-text-input
                id="otp"
                name="otp"
                type="text"
                inputmode="numeric"
                maxlength="6"
                pattern="[0-9]{6}"
                autocomplete="one-time-code"
                class="mt-1 w-full text-center text-xl tracking-[0.4em]"
                required
                autofocus
            />

            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Verify email
        </x-primary-button>
    </form>

    <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-4">
        @csrf

        <button
            type="submit"
            class="w-full text-center text-sm font-medium text-indigo-600 hover:text-indigo-700"
        >
            Resend verification code
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already verified?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600">
            Sign in
        </a>
    </p>
</x-guest-layout>
