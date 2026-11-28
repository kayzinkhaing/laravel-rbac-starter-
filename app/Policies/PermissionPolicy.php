<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Support\AdminDashboardAccess;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage permissions');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $permission->guard_name === 'web'
            && $user->can('manage permissions');
    }

    public function create(User $user): bool
    {
        return $user->can('manage permissions');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $permission->guard_name === 'web'
            && $user->can('manage permissions')
            && ($permission->name !== 'manage system permissions'
                || AdminDashboardAccess::isSuperAdmin($user));
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $this->update($user, $permission);
    }
}
