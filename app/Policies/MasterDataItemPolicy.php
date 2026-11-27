<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MasterDataItem;

class MasterDataItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage master data');
    }

    public function view(User $user, MasterDataItem $item): bool
    {
        return $user->can('manage master data');
    }

    public function create(User $user): bool
    {
        return $user->can('manage master data');
    }

    public function update(User $user, MasterDataItem $item): bool
    {
        return $user->can('manage master data');
    }

    public function delete(User $user, MasterDataItem $item): bool
    {
        return $user->can('manage master data');
    }
}
