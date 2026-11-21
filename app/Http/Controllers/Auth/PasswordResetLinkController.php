<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use App\Support\ReCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Show the password reset request form.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending a password reset OTP.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! ReCaptcha::verify($request->input('recaptcha_token'), 'forgot_password')) {
            return back()->withErrors(['recaptcha_token' => 'Security verification failed.']);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $this->passwordResetService->generateOtp($validated['email']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $maskedEmail = $this->passwordResetService->maskEmail($validated['email']);

        return redirect()->route('password.reset', [
            'email' => $validated['email'],
            'masked' => $maskedEmail,
        ]);
    }

    public function storeWeb(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        if (! ReCaptcha::verify($request->input('recaptcha_token'), 'forgot_password')) {
            return back()->withInput($request->only('email'))
                ->withErrors(['recaptcha_token' => 'Security verification failed. Please try again.']);
        }

        $status = Password::sendResetLink($validated);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
