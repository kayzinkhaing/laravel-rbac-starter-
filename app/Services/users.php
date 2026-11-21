<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class users extends common
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
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

    public function getByEmail($email)
    {
        return $this->userRepository->getByEmail($email);
    }

    public function paginateForAdministration(array $filters): LengthAwarePaginator
    {
        $query = $this->userRepository->query()
            ->select(['id', 'first_name', 'middle_name', 'last_name', 'email', 'email_verified_at', 'created_at'])
            ->with([
                'roles' => fn ($roles) => $roles
                    ->select(['roles.id', 'roles.name', 'roles.guard_name'])
                    ->with(['permissions' => fn ($permissions) => $permissions->select('permissions.id')]),
                'permissions' => fn ($permissions) => $permissions->select('permissions.id'),
            ])
            ->withCount('roles');

        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function (Builder $query) use ($term): void {
                $query->where('first_name', 'like', "%{$term}%")
                    ->orWhere('middle_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhereHas('roles', fn (Builder $roles) => $roles->where('name', 'like', "%{$term}%"));
            });
        }

        if (! empty($filters['role'])) {
            $query->whereHas('roles', fn (Builder $query) => $query->where('roles.id', $filters['role']));
        }

        if (($filters['verified'] ?? null) === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif (($filters['verified'] ?? null) === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        if ($sort === 'name') {
            $query->orderBy('last_name', $direction)->orderBy('first_name', $direction);
        } elseif (in_array($sort, ['email', 'email_verified_at', 'created_at', 'roles_count'], true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }

        return $query->paginate(15)->withQueryString();
    }

    public function loadForAdministration(User $user): User
    {
        return $user->load(['roles.permissions', 'permissions']);
    }

    public function createForAdministration(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $roles = $data['roles'] ?? [];
            unset($data['roles'], $data['roles_present']);

            $user = $this->userRepository->create($data);
            $user->syncRoles($roles);

            return $user->load('roles');
        });
    }

    public function updateForAdministration(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $roles = $data['roles'] ?? [];
            unset($data['roles'], $data['roles_present']);

            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }

            $user->update($data);
            $user->syncRoles($roles);

            return $user->refresh()->load('roles');
        });
    }

    public function deleteForAdministration(User $user): void
    {
        $user->delete();
    }
}
