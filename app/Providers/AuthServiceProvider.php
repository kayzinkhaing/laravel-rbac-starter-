<?php

namespace App\Providers;

use App\Models\ClientProfile;
use App\Models\CommonTable;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\ClientProfilePolicy;
use App\Policies\CommonTablePolicy;
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
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('access-admin-dashboard', [AdminDashboardAccess::class, 'allows']);
    }
}
