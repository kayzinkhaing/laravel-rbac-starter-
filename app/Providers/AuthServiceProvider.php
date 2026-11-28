<?php

namespace App\Providers;

use App\Models\ClientProfile;
use App\Models\CommonTable;
use App\Models\MasterDataCategory;
use App\Models\MasterDataItem;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\ClientProfilePolicy;
use App\Policies\CommonTablePolicy;
use App\Policies\MasterDataCategoryPolicy;
use App\Policies\MasterDataItemPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Support\AdminDashboardAccess;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Organization::class => OrganizationPolicy::class,
        ClientProfile::class => ClientProfilePolicy::class,
        CommonTable::class => CommonTablePolicy::class,
        MasterDataCategory::class => MasterDataCategoryPolicy::class,
        MasterDataItem::class => MasterDataItemPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /*
         * Scope permission checks inside role-prefixed dashboards to the
         * selected role plus direct user permissions. This prevents a
         * permission inherited from another assigned role from leaking
         * into the active role's dashboard.
         */
        Gate::before(function (User $user, string $ability): ?bool {
            if (! request()->routeIs('role.*')) {
                return null;
            }

            $activeRole = request()->attributes->get('activeRole');

            if (! $activeRole instanceof Role) {
                return null;
            }

            $isWebPermission = Permission::query()
                ->where('guard_name', 'web')
                ->where('name', $ability)
                ->exists();

            if (! $isWebPermission) {
                return null;
            }

            return $activeRole->hasPermissionTo($ability, 'web')
                || $user->hasDirectPermission($ability);
        });

        Gate::define('access-admin-dashboard', [AdminDashboardAccess::class, 'allows']);
    }
}
