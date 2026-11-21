<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\roleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class roles extends common
{
    protected $roleRepository;

    public function __construct(roleRepository $roleRepository)
    {
        parent::__construct($roleRepository);
        $this->roleRepository = $roleRepository;
    }

    // Role-specific logic, not CRUD
    public function assignRoleToUser($userId, $roleId)
    {
        $user = User::find($userId);
        $role = Role::find($roleId);

        if ($user && $role) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function getRoleById($id)
    {
        return $this->roleRepository->getRoleById($id);
    }

    public function paginateWithPermissions(array $filters = []): LengthAwarePaginator
    {
        $query = $this->roleRepository->query()
            ->where('guard_name', 'web')
            ->select(['id', 'name', 'guard_name'])
            ->with(['permissions' => fn ($permissions) => $permissions
                ->select(['permissions.id', 'permissions.name'])
                ->orderBy('permissions.name')])
            ->withCount(['users', 'permissions']);

        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function ($query) use ($term): void {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhereHas('permissions', fn ($permissions) => $permissions->where('name', 'like', "%{$term}%"));
            });
        }

        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';

        if (in_array($sort, ['name', 'users_count', 'permissions_count'], true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('name');
        }

        return $query
            ->paginate(15)
            ->withQueryString();
    }

    public function createWithPermissions(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = $this->roleRepository->create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($this->resolveWebPermissions($data['permissions'] ?? []));

            return $role;
        });
    }

    public function updateWithPermissions(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $this->ensureWebGuard($role);
            $role->update(['name' => $data['name']]);
            $role->syncPermissions($this->resolveWebPermissions($data['permissions'] ?? []));

            return $role->refresh()->load('permissions');
        });
    }

    /**
     * @param  array<int, int|string|Permission>  $permissionIds
     * @return array<int, Permission>
     */
    private function resolveWebPermissions(array $permissionIds): array
    {
        $ids = collect($permissionIds)->map(function ($permission): int {
            if ($permission instanceof Permission) {
                if ($permission->guard_name !== 'web') {
                    throw ValidationException::withMessages([
                        'permissions' => 'Only web-guard permissions can be assigned to this role.',
                    ]);
                }

                return (int) $permission->getKey();
            }

            if (is_int($permission) || (is_string($permission) && ctype_digit($permission))) {
                return (int) $permission;
            }

            throw ValidationException::withMessages([
                'permissions' => 'Select valid permissions.',
            ]);
        });

        if ($ids->unique()->count() !== $ids->count()) {
            throw ValidationException::withMessages([
                'permissions' => 'Permissions must be unique.',
            ]);
        }

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereKey($ids)
            ->get()
            ->keyBy(fn (Permission $permission): int => (int) $permission->getKey());

        if ($permissions->count() !== $ids->count()) {
            throw ValidationException::withMessages([
                'permissions' => 'One or more selected permissions are unavailable for the web guard.',
            ]);
        }

        return $ids->map(fn (int $id): Permission => $permissions->get($id))->all();
    }

    private function ensureWebGuard(Role $role): void
    {
        if ($role->guard_name !== 'web') {
            throw ValidationException::withMessages([
                'role' => 'Only web-guard roles can be managed here.',
            ]);
        }
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }
}
