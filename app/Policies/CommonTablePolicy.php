<?php

namespace App\Policies;

use App\Models\CommonTable;
use App\Models\User;

class CommonTablePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view common tables');
    }

    public function view(User $user, CommonTable $commonTable): bool
    {
        return $user->can('view common tables');
    }

    public function create(User $user): bool
    {
        return $user->can('manage common tables');
    }

    public function update(User $user, CommonTable $commonTable): bool
    {
        return $user->can('manage common tables');
    }

    public function delete(User $user, CommonTable $commonTable): bool
    {
        return $user->can('manage common tables');
    }
}
