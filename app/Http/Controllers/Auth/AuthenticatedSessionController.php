<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Support\AdminDashboardAccess;
use App\Support\ReCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! ReCaptcha::verify($request->input('recaptcha_token'), 'login')) {
            return back()->withInput($request->only('email'))
                ->withErrors(['recaptcha_token' => 'Security verification failed. Please try again.']);
        }

        $user = $request->validateCredentials();

        if ($user->two_factor_enabled) {
            $request->session()->regenerate();
            $request->session()->put('2fa:web:user:id', $user->getKey());

            return redirect()->route('twofactor.verify');
        }

        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->to(AdminDashboardAccess::dashboardUrlFor($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function apiLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        return $this->authService->apiLogin($credentials, $request);
    }

    public function apiLogout(Request $request)
    {
        return $this->authService->apiLogout($request);
    }
}
