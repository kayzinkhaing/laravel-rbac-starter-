<?php

namespace App\Http\Middleware;

use App\Support\AdminDashboardAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ResolveRoleDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = (string) $request->route('roleSlug');
        $resolved = AdminDashboardAccess::resolveSlug($slug);

        if (! $resolved['role']) {
            abort(404);
        }

        $role = $resolved['role'];
        $user = $request->user();
        $availableRoles = AdminDashboardAccess::assignedRoles($user);

        if (! $availableRoles->contains('id', $role->getKey())) {
            abort(403);
        }

        $activeRole = $availableRoles->firstWhere('id', $request->session()->get('active_role_id'))
            ?? $availableRoles->first();

        if (! $activeRole || $activeRole->getKey() !== $role->getKey()) {
            abort(403);
        }

        $request->session()->put('active_role_id', $activeRole->getKey());
        $request->attributes->set('activeRole', $role);
        $request->attributes->set('availableRoles', $availableRoles);
        $request->attributes->set(
            'roleSlugs',
            AdminDashboardAccess::slugsForRoles($availableRoles, $resolved['allRoles'])
        );
        URL::defaults(['roleSlug' => $slug]);
        view()->share('activeRole', $role);
        view()->share('availableRoles', $availableRoles);
        view()->share('roleSlugs', $request->attributes->get('roleSlugs'));

        return $next($request);
    }
}
