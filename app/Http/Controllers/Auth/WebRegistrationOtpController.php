<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebRegistrationOtpController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    public function show(Request $request): RedirectResponse|View
    {
        $email = $request->session()->get('pending_registration_email');

        if (! $email) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', compact('email'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $email = $request->session()->get('pending_registration_email');

        if (! $email) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Your registration session expired. Please register again.']);
        }

        $user = $this->registrationService->verifyWebCode(
            $email,
            $request->input('otp')
        );

        if (! $user) {
            return back()->withErrors([
                'otp' => 'The OTP is invalid or expired. Please try again.',
            ]);
        }

        $request->session()->forget('pending_registration_email');

        return redirect()->route('login')
            ->with('status', 'Email verified successfully. Please log in.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('pending_registration_email');

        if (! $email) {
            return redirect()->route('register');
        }

        $sent = $this->registrationService->resendWebCode($email);

        if (! $sent) {
            return back()->withErrors([
                'otp' => 'Please wait before requesting another OTP, or register again if the code expired.',
            ]);
        }

        return back()->with('status', 'A new verification code has been sent.');
    }
}
