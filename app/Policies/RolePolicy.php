<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $role->guard_name === 'web'
            && $user->can('manage roles');
    }

    public function create(User $user): bool
    {
        return $user->can('manage roles');
    }

    public function update(User $user, Role $role): bool
    {
        return $role->guard_name === 'web'
            && $user->can('manage roles')
            && array_diff($role->permissions->modelKeys(), $user->getAllPermissions()->modelKeys()) === [];
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }
}
