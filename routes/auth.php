<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebRegistrationOtpController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
| Routes that only guests (not logged-in users) can access
*/

Route::middleware('guest')->group(function () {

    // 🔑 Register / Login
    Route::get('register', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'storeWeb']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // 🔐 Forgot Password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'storeWeb'])->name('password.email');

    Route::get('reset-password/{token?}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'storeWeb'])->name('password.store');

    // 🔑 Social Login
    Route::get('auth/google', [OAuthController::class, 'redirectToGoogle'])->name('google');
    Route::get('auth/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    Route::get('auth/github', [OAuthController::class, 'redirectToGithub'])->name('github');
    Route::get('auth/github/callback', [OAuthController::class, 'handleGithubCallback'])->name('github.callback');

    Route::get('register/verify-otp', [
        WebRegistrationOtpController::class,
        'show',
    ])->name('register.otp');

    Route::post('register/verify-otp', [
        WebRegistrationOtpController::class,
        'verify',
    ])->middleware('throttle:6,1')->name('register.otp.verify');

    Route::post('register/resend-otp', [
        WebRegistrationOtpController::class,
        'resend',
    ])->middleware('throttle:3,1')->name('register.otp.resend');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
| Routes that only logged-in users can access
*/
Route::middleware('auth')->group(function () {

    // 🔐 Email verification
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // 🔑 Confirm password
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('two-factor/enable', [TwoFactorController::class, 'enable'])->name('twofactor.enable');
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable'])->name('twofactor.disable');
    Route::get('two-factor/setup', [TwoFactorController::class, 'setup'])->name('twofactor.setup');

    // 🔓 Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('auth/verify', [TwoFactorController::class, 'showVerifyForm'])->name('twofactor.verify');
    Route::post('auth/verify', [TwoFactorController::class, 'verifyCode'])
        ->middleware('throttle:6,1')
        ->name('twofactor.verify.post');
});
