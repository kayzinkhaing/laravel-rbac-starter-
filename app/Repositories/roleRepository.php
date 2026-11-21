<?php

namespace App\Repositories;

use App\Models\Role;
use App\Contracts\roleInterface;

class roleRepository extends baseRepository implements roleInterface
{
    public function __construct()
    {
        parent::__construct('Role');
    }

    public function getRolebyId($roleId)
    {
        return $this->getModelInstance()->findOrFail($roleId);
    }

    public function create(array $data): Role
    {
        return Role::query()->create($data);
    }
}
