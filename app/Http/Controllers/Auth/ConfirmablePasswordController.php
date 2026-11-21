<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController
{
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($validated['password'], $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended(AdminDashboardAccess::dashboardUrlFor($request->user()));
    }

    /**
     * Show the confirm password view.
     */
    public function showConfirm(Request $request): View
    {
        $email = $request->email;

        // dd($email);
        return view('auth.confirm-password', compact('email'));
    }

    /**
     * Confirm the user's password.
     */
    public function passwordchange(Request $request): RedirectResponse
    {
        // $validated = $request->validate([
        //     'email' => 'required|email',
        //     'password' => ['required', 'confirmed'],
        // ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();
        // dd($user);
        // if (!$user) {
        //     return back()->withErrors(['email' => 'Email not found.']);
        // }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        // dd('hi');

        // Redirect to login
        return redirect()->route('login')->with('success', 'Password updated successfully.');
    }
}
