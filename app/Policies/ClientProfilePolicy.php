<?php

namespace App\Policies;

use App\Models\ClientProfile;
use App\Models\User;

class ClientProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage client profiles');
    }

    public function view(User $user, ClientProfile $profile): bool
    {
        return $user->is($profile->user)
            || $user->can('manage client profiles');
    }

    public function create(User $user): bool
    {
        return $user->can('manage client profiles');
    }

    public function update(User $user, ClientProfile $profile): bool
    {
        return $user->can('manage client profiles');
    }

    public function delete(User $user, ClientProfile $profile): bool
    {
        return $user->can('manage client profiles');
    }
}
