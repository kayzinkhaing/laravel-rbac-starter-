<?php

namespace App\Http\Middleware;

use App\Support\AdminDashboardAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ShareActiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $user->loadMissing('media');
            $roles = $request->attributes->get('availableRoles') ?? AdminDashboardAccess::assignedRoles($user);
            $selectedRoleId = $request->session()->get('active_role_id');
            $activeRole = $roles->firstWhere('id', $selectedRoleId) ?? $roles->first();
            $roleSlugs = $request->attributes->get('roleSlugs')
                ?? AdminDashboardAccess::slugsForRoles($roles);

            if ($activeRole) {
                $request->session()->put('active_role_id', $activeRole->id);
                URL::defaults(['roleSlug' => $roleSlugs[$activeRole->id]]);
            } else {
                $request->session()->forget('active_role_id');
            }

            view()->share('activeRole', $activeRole);
            view()->share('availableRoles', $roles);
            view()->share('roleSlugs', $roleSlugs);
        }

        return $next($request);
    }
}
