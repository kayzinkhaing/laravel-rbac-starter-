<?php

namespace App\Http\Controllers\Web;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController
{
    public function __invoke(): View|RedirectResponse
    {
        $user = request()->user();
        $activeRole = request()->attributes->get('activeRole');

        if (request()->routeIs('dashboard')) {
            return redirect()->to(AdminDashboardAccess::dashboardUrlFor($user));
        }

        $stats = [];

        if ($user->can('manage users')) {
            $userCounts = User::query()
                ->selectRaw('COUNT(*) AS total, SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) AS verified')
                ->first();
            $stats[] = ['label' => 'Total users', 'value' => $userCounts->total, 'icon' => 'users-round', 'tone' => 'indigo'];
            $stats[] = ['label' => 'Verified accounts', 'value' => $userCounts->verified, 'icon' => 'check-circle', 'tone' => 'emerald'];
        }

        if ($user->can('manage roles')) {
            $stats[] = ['label' => 'Roles', 'value' => Role::query()->where('guard_name', 'web')->count(), 'icon' => 'shield', 'tone' => 'amber'];
        }

        if ($user->can('manage permissions')) {
            $stats[] = ['label' => 'Permissions', 'value' => Permission::query()->where('guard_name', 'web')->count(), 'icon' => 'key-round', 'tone' => 'violet'];
        }

        return view('dashboard', [
            'canManageUsers' => $user->can('manage users'),
            'activeRole' => $activeRole,
            'recentUsers' => $user->can('manage users')
                ? User::query()->latest()->limit(6)->get(['id', 'first_name', 'middle_name', 'last_name', 'email', 'created_at'])
                : collect(),
            'stats' => $stats,
        ]);
    }
}
