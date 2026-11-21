<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage organizations');
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->can('manage organizations')
            || $user->organizations()->whereKey($organization->getKey())->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('manage organizations');
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->can('manage organizations');
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $user->can('manage organizations');
    }
}
