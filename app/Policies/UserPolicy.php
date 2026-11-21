<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can('manage users');
    }

    public function create(User $actor): bool
    {
        return $actor->can('manage users');
    }

    public function view(User $actor, User $user): bool
    {
        return $actor->can('manage users') || $actor->is($user);
    }

    public function update(User $actor, User $user): bool
    {
        return $actor->is($user)
            || ($actor->can('manage users') && $this->canManageAccessOf($actor, $user));
    }

    public function delete(User $actor, User $user): bool
    {
        return $actor->is($user)
            || ($actor->can('manage users') && $this->canManageAccessOf($actor, $user));
    }

    private function canManageAccessOf(User $actor, User $user): bool
    {
        return array_diff(
            $user->getAllPermissions()->modelKeys(),
            $actor->getAllPermissions()->modelKeys()
        ) === [];
    }
}
