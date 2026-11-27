<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Support\AdminDashboardAccess;
use App\Traits\HasMedia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    use AuthorizesRequests;
    use HasMedia;

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $this->authorize('view', $request->user());

        if ($redirect = $this->legacyProfileRedirect($request)) {
            return $redirect;
        }

        $request->user()->loadMissing(['roles', 'media']);

        return view('profile.edit', [
            'user' => $request->user(),
            'roleDashboard' => $request->routeIs('role.profile'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, ?string $roleSlug = null): RedirectResponse
    {
        if ($redirect = $this->legacyProfileRedirect($request)) {
            return $redirect;
        }

        $this->authorize('update', $request->user());

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::to($this->profileUrl($roleSlug))->with('status', 'profile-updated');
    }

    public function updateImage(Request $request, ?string $roleSlug = null): RedirectResponse
    {
        if ($redirect = $this->legacyProfileRedirect($request)) {
            return $redirect;
        }

        $user = $request->user();
        $this->authorize('update', $user);

        $validated = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->updateMedia($user, $validated['image']);

        return Redirect::to($this->profileUrl($roleSlug))->with('status', 'profile-image-updated');
    }

    public function deleteImage(Request $request, ?string $roleSlug = null): RedirectResponse
    {
        if ($redirect = $this->legacyProfileRedirect($request)) {
            return $redirect;
        }

        $user = $request->user();
        $this->authorize('update', $user);

        $this->deleteMedia($user);

        return Redirect::to($this->profileUrl($roleSlug))->with('status', 'profile-image-removed');
    }

    public function image(Request $request, User $user): StreamedResponse
    {
        $this->authorize('view', $user);

        $media = $user->profileImage();
        if (! $media || ! Storage::disk('private')->exists($media->url)) {
            abort(404);
        }

        return Storage::disk('private')->response(
            $media->url,
            $media->original_filename,
            ['Cache-Control' => 'private, max-age=3600']
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request, ?string $roleSlug = null): RedirectResponse
    {
        if ($redirect = $this->legacyProfileRedirect($request)) {
            return $redirect;
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $this->authorize('delete', $user);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function profileUrl(?string $roleSlug): string
    {
        return $roleSlug
            ? route('role.profile', ['roleSlug' => $roleSlug])
            : AdminDashboardAccess::profileUrlFor(request()->user());
    }

    private function legacyProfileRedirect(Request $request): ?RedirectResponse
    {
        if (! $request->routeIs('profile.*')) {
            return null;
        }

        $role = AdminDashboardAccess::activeRole($request->user());

        return $role
            ? Redirect::to(route('role.profile', ['roleSlug' => AdminDashboardAccess::slugFor($role)]), 307)
            : null;
    }
}
