```blade
<x-guest-layout>
    @php
        $email = auth()->user()->email ?? '';

        if ($email && str_contains($email, '@')) {
            [$localPart, $domain] = explode('@', $email, 2);

            $visibleStart = substr($localPart, 0, min(2, strlen($localPart)));
            $maskedPart = str_repeat('*', max(3, strlen($localPart) - strlen($visibleStart)));

            $maskedEmail = $visibleStart . $maskedPart . '@' . $domain;
        } else {
            $maskedEmail = '';
        }
    @endphp

    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">
            One last step
        </p>

        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
            {{ __('Verify your email') }}
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            {{ __('Open the verification link we sent to your email address to finish setting up your account. If it has not arrived, you can request another.') }}
        </p>

        @if ($maskedEmail)
            <p class="mt-4 rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <span class="font-medium text-slate-700">Email address:</span>
                <span class="font-semibold text-indigo-600">{{ $maskedEmail }}</span>
            </p>
        @endif
    </div>

    @if (session('status') == 'verification-link-sent')
        <div
            role="status"
            class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-3 text-sm font-medium text-emerald-800"
        >
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button class="w-full">
                {{ __('Resend verification email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf

            <button
                type="submit"
                class="rounded-md px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            >
                {{ __('Sign out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
```
