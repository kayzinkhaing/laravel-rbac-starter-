<?php

namespace App\Repositories;

use App\Contracts\permissionInterface;
use App\Models\Permission;

class permissionRepository extends baseRepository implements permissionInterface
{
    public function __construct()
    {
        parent::__construct(class_basename('Permission'));
    }

    public function getPermissionbyId($permissionId)
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->findOrFail($permissionId);
    }

    public function create(array $data): Permission
    {
        return Permission::query()->create($data);
    }
}
