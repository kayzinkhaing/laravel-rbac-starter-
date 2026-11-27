<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MasterDataCategory;

class MasterDataCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage master data');
    }

    public function view(User $user, MasterDataCategory $category): bool
    {
        return $user->can('manage master data');
    }

    public function create(User $user): bool
    {
        return $user->can('manage master data');
    }

    public function update(User $user, MasterDataCategory $category): bool
    {
        return $user->can('manage master data');
    }

    public function delete(User $user, MasterDataCategory $category): bool
    {
        return $user->can('manage master data');
    }
}
