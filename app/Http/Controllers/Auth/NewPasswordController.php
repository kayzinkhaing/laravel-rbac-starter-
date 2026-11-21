<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use App\Support\ReCaptcha;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function storeWeb(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    /**
     * Display the password reset view.
     */
    public function showForget(Request $request): View
    {
        $email = $request->email;
        $masked = $request->masked;

        return view('auth.reset-password', compact('email', 'masked'));
    }

    /**
     * Handle OTP verification.
     */
    public function verifyForgotcode(VerifyOtpRequest $request): RedirectResponse
    {
        if (! ReCaptcha::verify($request->input('recaptcha_token'), 'forgot_password')) {
            return back()->withErrors(['recaptcha_token' => 'Security verification failed.']);
        }

        $verificationCode = implode('', $request->otp);

        if (! $this->passwordResetService->verifyCode($request->email, $verificationCode)) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        return redirect()->route('change.password.form', [
            'email' => $request->email,
        ]);
    }

    /**
     * Resend OTP code.
     */
    public function resendCodePassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        try {
            $this->passwordResetService->resendCode($request->email);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => $e->getMessage()]);
        }

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}
