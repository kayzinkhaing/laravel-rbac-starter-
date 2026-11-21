<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class OAuthController extends Controller
{
    // -------------------
    // Google OAuth
    // -------------------
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $providerUser = Socialite::driver('google')->user();

            return $this->loginOrRegister($providerUser, 'google');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')
                ->withErrors(['oauth' => 'Google sign-in could not be completed. Please try again.']);
        }
    }

    // -------------------
    // GitHub OAuth
    // -------------------
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $providerUser = Socialite::driver('github')->user();

            return $this->loginOrRegister($providerUser, 'github');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')
                ->withErrors(['oauth' => 'GitHub sign-in could not be completed. Please try again.']);
        }
    }

    // -------------------
    // Login or register user
    // -------------------
    private function loginOrRegister($providerUser, $provider)
    {
        if (! $providerUser->email) {
            return redirect()->route('login')
                ->withErrors(['oauth' => 'This provider account did not return an email address.']);
        }

        // Check if user exists by email
        $user = User::where('email', $providerUser->email)->first();

        if ($user) {
            $action = 'login';
        } else {
            // New user → register
            $user = User::create([
                'name' => $providerUser->name ?? $providerUser->nickname,
                'email' => $providerUser->email,
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ]);

            $action = 'register';

        }
        // ------------------------------

        // -------------------------------
        // Store login type and action in session
        // -------------------------------
        session([
            'login_method' => $provider, // google, github
            'login_action' => $action,   // login or register
        ]);

        // -------------------------------
        // Store token in oauth_access_tokens table (optional)
        // -------------------------------
        // $clientId = DB::table('oauth_clients')
        //     ->where('name', 'like', '%' . $provider . '%')
        //     ->value('id') ?? 1; // default client id

        // $lastId = DB::table('oauth_access_tokens')->max('id') ?? 0;
        // $newId = $lastId + 1;

        // DB::table('oauth_access_tokens')->insert(
        //     [
        //         'id' => $newId,
        //         'user_id' => $user->id,
        //         'client_id' => $clientId,
        //         'name' => $provider,
        //         'revoked' => 0,
        //         'scopes' => null,
        //         'expires_at' => Carbon::now()->addHours(1),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]
        // );
        // 2FA check
        // -------------------------------
        if ($user->two_factor_enabled) {
            // Temporarily store user id in session for 2FA verification
            session()->regenerate();
            session(['2fa:web:user:id' => $user->id]);

            return redirect()->route('twofactor.verify');
        }
        // -------------------------------
        // Redirect to home/dashboard
        // -------------------------------
        Auth::login($user);
        session()->regenerate();

        return redirect()->to(AdminDashboardAccess::dashboardUrlFor($user));
    }
}
