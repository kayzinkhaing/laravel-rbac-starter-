<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\RegistrationService;
use App\Support\AdminDashboardAccess;
use App\Support\ReCaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function storeWeb(RegisterRequest $request): RedirectResponse
    {
        if (! ReCaptcha::verify($request->input('recaptcha_token'), 'register')) {
            return back()->withInput($request->only('name', 'email'))
                ->withErrors(['recaptcha_token' => 'Security verification failed. Please try again.']);
        }

        $user = User::create($request->validated());
        event(new Registered($user));
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect()->to(AdminDashboardAccess::dashboardUrlFor($user));
    }

    /**
     * ✅ Register user (API)
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! ReCaptcha::verify($request->input('recaptcha_token'), 'register')) {
            return response()->json([
                'success' => false,
                'message' => 'Security verification failed',
            ], 422);
        }

        // hash password
        $validated['password'] = bcrypt($validated['password']);

        // store temp user + send OTP
        $this->registrationService->storeTempUser(
            $validated,
            $request->file('image')
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to email',
            'email' => $validated['email'],
        ], 200);
    }

    /**
     * ✅ Resend OTP (API)
     */
    public function resendCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $code = $this->registrationService->resendCode($request->email);

        if (! $code) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot resend OTP now. Try later.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP resent successfully',
        ], 200);
    }
}
