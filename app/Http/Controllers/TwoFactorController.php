<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorController extends Controller
{
    public function setup(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectToActiveRoleRoute($request, 'setup')) {
            return $redirect;
        }

        $user = $request->user();
        $google2fa = new Google2FA;

        if (! $user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('profile.two-factor', [
            'user' => $user,
            'qrCode' => $qrCode,
            'newRecoveryCodes' => session('new_recovery_codes', []),
            'roleDashboard' => $request->routeIs('role.twofactor.setup'),
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $google2fa = new Google2FA;

        // 1. Always ensure a secret exists
        if (! $user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        // 2. Initialize variable to prevent "Undefined variable" error
        $qrCode = '';

        // 3. Only generate QR if 2FA is not yet enabled
        if (! $user->two_factor_enabled) {
            $qrCode = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );
        }

        // Pass it to the index view
        return view('profile.index', compact('user', 'qrCode'));
    }

    public function enable(Request $request)
    {
        if ($redirect = $this->redirectToActiveRoleRoute($request, 'enable')) {
            return $redirect;
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();
        $google2fa = new Google2FA;

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if ($valid) {
            $plainRecoveryCodes = collect(range(1, 10))
                ->map(fn () => Str::upper(Str::random(10)))
                ->all();

            $user->two_factor_enabled = true;
            $user->two_factor_recovery_codes = array_map(
                fn (string $code) => Hash::make($code),
                $plainRecoveryCodes
            );
            $user->save();

            return redirect()->route($this->setupRouteName($request))
                ->with('success', 'Two-factor authentication enabled.')
                ->with('new_recovery_codes', $plainRecoveryCodes);
        }

        return back()->with('error', 'Invalid verification code.');
    }

    public function disable(Request $request)
    {
        if ($redirect = $this->redirectToActiveRoleRoute($request, 'disable')) {
            return $redirect;
        }

        $user = $request->user();
        $user->two_factor_enabled = false;
        $user->two_factor_recovery_codes = null;
        $user->save();

        $response = $request->routeIs('role.twofactor.disable')
            ? redirect()->route('role.twofactor.setup')
            : back();

        return $response->with('success', 'Two-factor authentication disabled.');
    }

    public function showVerifyForm(): View|RedirectResponse
    {
        $user = User::find(session('2fa:web:user:id'));

        if (! $user?->two_factor_enabled) {
            session()->forget('2fa:web:user:id');

            return redirect()->route('login');
        }

        return view('auth.verify'); // create this Blade
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:6', 'max:20'],
        ]);

        $webVerification = $request->routeIs('twofactor.verify.post');
        $sessionKey = $webVerification ? '2fa:web:user:id' : '2fa:user:id';
        $user = User::find(session($sessionKey));

        if (! $user?->two_factor_enabled) {
            session()->forget($sessionKey);

            return redirect()->route('login')->withErrors(['code' => 'Session expired, please login again.']);
        }

        $google2fa = app('pragmarx.google2fa');

        $isValid = preg_match('/^\d{6}$/', $request->code) === 1
            ? $google2fa->verifyKey($user->google2fa_secret, $request->code)
            : false;

        if (! $isValid && is_array($user->two_factor_recovery_codes)) {
            $remainingCodes = $user->two_factor_recovery_codes;

            foreach ($remainingCodes as $index => $hashedCode) {
                if (Hash::check(strtoupper($request->code), $hashedCode)) {
                    $isValid = true;
                    unset($remainingCodes[$index]);
                    $user->two_factor_recovery_codes = array_values($remainingCodes);
                    $user->save();
                    break;
                }
            }
        }

        if (! $isValid) {
            return back()->withErrors(['code' => 'Invalid verification code.']); // <-- ERROR MESSAGE
        }

        // If valid → complete login
        session()->forget($sessionKey);
        Auth::login($user);
        if ($webVerification) {
            $request->session()->regenerate();
        }

        return redirect()->to(AdminDashboardAccess::dashboardUrlFor($user));
    }

    private function redirectToActiveRoleRoute(Request $request, string $action): ?RedirectResponse
    {
        if (
            $request->routeIs('role.twofactor.*')
            || ! $request->routeIs('twofactor.setup', 'twofactor.enable', 'twofactor.disable')
        ) {
            return null;
        }

        $role = AdminDashboardAccess::activeRole($request->user());

        if (! $role) {
            return null;
        }

        return redirect()->route("role.twofactor.{$action}", [
            'roleSlug' => AdminDashboardAccess::slugFor($role),
        ], 307);
    }

    private function setupRouteName(Request $request): string
    {
        return $request->routeIs('role.twofactor.*')
            ? 'role.twofactor.setup'
            : 'twofactor.setup';
    }
}
