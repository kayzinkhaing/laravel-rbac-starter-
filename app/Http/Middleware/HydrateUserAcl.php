<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HydrateUserAcl
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $media = $user->profileImage();

            $user->setAttribute('image', $media
                ? route('profile.image', ['user' => $user])
                : asset('images/default-avatar.png'));
            $user->syncOriginalAttribute('image');
        }

        return $next($request);
    }
}
