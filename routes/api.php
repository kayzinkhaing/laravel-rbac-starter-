<?php

use App\Http\Controllers\Api\ClientProfileController;
use App\Http\Controllers\Api\CommonTableController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

// --------------------------------------
// 🔓 GUEST ROUTES (NO TOKEN)
// --------------------------------------
Route::middleware('guest')->prefix('auth')->group(function () {

    Route::post('register', [RegisteredUserController::class, 'register']);

    // ✅ FIXED (only one login)
    Route::post('login', [AuthenticatedSessionController::class, 'apiLogin']);

    Route::post('verify-email', [OtpVerificationController::class, 'verify']);
    Route::post('resend-code', [RegisteredUserController::class, 'resendCode']);

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('reset-password', [NewPasswordController::class, 'verifyForgotcode']);
    Route::post('resend-code-password', [NewPasswordController::class, 'resendCodePassword']);

    Route::get('google', [OAuthController::class, 'redirectToGoogle']);
    Route::get('github', [OAuthController::class, 'redirectToGithub']);
});

// --------------------------------------
// 🔐 AUTH ROUTES (TOKEN REQUIRED)
// --------------------------------------
Route::middleware(['auth:api', 'acl'])->prefix('v1')->group(function () {

    Route::get('verify-email', EmailVerificationPromptController::class);

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class])
        ->middleware(['signed', 'throttle:6,1']);

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'passwordConfirm']);
    Route::post('change-password', [ConfirmablePasswordController::class, 'passwordchange']);
    Route::put('password', [PasswordController::class, 'update']);

    Route::post('two-factor/enable', [TwoFactorController::class, 'enable']);
    Route::post('two-factor/disable', [TwoFactorController::class, 'disable']);
    Route::post('two-factor/verify', [TwoFactorController::class, 'verifyCode']);
});

// --------------------------------------
// 📦 PROTECTED API MODULE ROUTES
// --------------------------------------
Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'apiLogout']);

    Route::apiResource('users', UserController::class);
    Route::get('organizations', [OrganizationController::class, 'index']);
    Route::get('organizations/{id}', [OrganizationController::class, 'show']);
    // Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('client-profiles', ClientProfileController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('common-tables', CommonTableController::class);
});
