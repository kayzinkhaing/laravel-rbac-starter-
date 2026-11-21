@extends(($roleDashboard ?? false) ? 'layouts.app' : 'layouts.public')

@section('title', 'Two-factor authentication')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-7">
        <p class="text-sm font-medium text-indigo-600">Account security</p>
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Two-factor authentication</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Protect sign-in with a one-time authenticator code.</p>
    </div>

    @if (session('success'))
        <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div role="alert" class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    @if (! $user->two_factor_enabled)
        <div class="surface-card overflow-hidden">
            <div class="grid gap-6 p-5 sm:grid-cols-[12rem_minmax(0,1fr)] sm:p-6">
                <div class="qr-code-panel rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">
                    <img src="{{ $qrCode }}" alt="Authenticator setup QR code" class="mx-auto block h-36 w-36">
                    <p class="mt-3 text-xs text-slate-500">Scan with your authenticator app</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Confirm setup</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Enter the six-digit code currently shown by your authenticator app.</p>
                    <form method="POST" action="{{ route(($roleDashboard ?? false) ? 'role.twofactor.enable' : 'twofactor.enable') }}" class="mt-5">
                        @csrf
                        <label for="code" class="field-label">Authenticator code</label>
                        <input id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus class="input-control">
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        <button type="submit" class="btn-primary mt-4">Enable two-factor authentication</button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="surface-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Active
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Sign-in codes are required whenever your password is used.</p>
                </div>
                <form method="POST" action="{{ route(($roleDashboard ?? false) ? 'role.twofactor.disable' : 'twofactor.disable') }}">
                    @csrf
                    <button type="submit" class="btn-danger">Disable 2FA</button>
                </form>
            </div>

            @if ($newRecoveryCodes)
                <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-900">Save these recovery codes</p>
                    <p class="mt-1 text-xs leading-5 text-amber-700">They are shown once. Each code can be used once if your authenticator is unavailable.</p>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($newRecoveryCodes as $code)
                            <code class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800">{{ $code }}</code>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</section>
@endsection
