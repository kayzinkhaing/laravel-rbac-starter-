<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Repositories\permissionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Permissions extends Common
{
    public function __construct(protected permissionRepository $permissionRepository)
    {
        parent::__construct($permissionRepository);
    }

    public function paginate(?string $search = null, string $sort = 'name', string $direction = 'asc'): LengthAwarePaginator
    {
        $query = $this->permissionRepository->query()
            ->select(['id', 'name', 'guard_name'])
            ->where('guard_name', 'web')
            ->withCount('roles')
            ->orderBy(in_array($sort, ['name', 'roles_count'], true) ? $sort : 'name', in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc');

        if ($search !== null && trim($search) !== '') {
            $query->where('name', 'like', '%'.trim($search).'%');
        }

        return $query->paginate(15)->withQueryString();
    }

    public function createPermission(array $data): Permission
    {
        return $this->permissionRepository->create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
    }

    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update(['name' => $data['name']]);

        return $permission->refresh();
    }

    public function deletePermission(Permission $permission): void
    {
        $permission->delete();
    }

    public function assignPermissionToRole(int $roleId, int $permissionId): Role
    {
        $role = Role::query()
            ->where('guard_name', 'web')
            ->findOrFail($roleId);
        $permission = Permission::query()
            ->where('guard_name', 'web')
            ->findOrFail($permissionId);
        $role->givePermissionTo($permission);

        return $role;
    }

    public function getPermissionById(int $id): ?Permission
    {
        return $this->permissionRepository->getPermissionbyId($id);
    }
}
