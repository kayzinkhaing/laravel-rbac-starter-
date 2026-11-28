<?php

namespace App\Support;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class AdminDashboardAccess
{
    /**
     * Dashboard entry is permission-driven.
     *
     * Any web-guard permission granted directly or through one of the
     * user's roles can make the dashboard available. Individual dashboard
     * sections and routes must still authorize their own permissions.
     */
    public static function allows(User $user): bool
    {
        return $user->getAllPermissions()
            ->contains(fn ($permission): bool => $permission->guard_name === 'web');
    }

    /**
     * A stable system capability, not a role-name check and not a list of
     * every module permission currently registered in the database.
     */
    public static function isSuperAdmin(User $user): bool
    {
        return $user->getAllPermissions()->contains(
            fn ($permission): bool => $permission->guard_name === 'web'
                && $permission->name === 'manage system permissions'
        );
    }

    /**
     * @return Collection<int, Role>
     */
    public static function assignedRoles(User $user): EloquentCollection
    {
        return $user->roles()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['roles.id', 'roles.name', 'roles.guard_name']);
    }

    public static function activeRole(User $user, ?int $preferredRoleId = null): ?Role
    {
        $roles = self::assignedRoles($user);
        $preferredRoleId ??= session('active_role_id');
        $role = $roles->firstWhere('id', $preferredRoleId) ?? $roles->first();

        if ($role) {
            session(['active_role_id' => $role->id]);
        } else {
            session()->forget('active_role_id');
        }

        return $role;
    }

    public static function dashboardUrlFor(User $user): string
    {
        $role = self::activeRole($user);

        return $role
            ? route('role.dashboard', ['roleSlug' => self::slugFor($role)])
            : route('home');
    }

    public static function profileUrlFor(User $user): string
    {
        $role = self::activeRole($user);

        return $role
            ? route('role.profile', ['roleSlug' => self::slugFor($role)])
            : route('profile.edit');
    }

    public static function slugFor(Role $role): string
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->get(['id', 'name', 'guard_name']);

        return self::slugsForRoles($roles, $roles)[$role->getKey()]
            ?? self::baseSlug($role);
    }

    /**
     * @param Collection<int, Role> $roles
     * @param Collection<int, Role>|null $allRoles
     * @return array<int, string>
     */
    public static function slugsForRoles(Collection $roles, ?Collection $allRoles = null): array
    {
        $allRoles ??= Role::query()
            ->where('guard_name', 'web')
            ->get(['id', 'name', 'guard_name']);
        $counts = $allRoles->countBy(fn (Role $candidate): string => self::baseSlug($candidate));

        return $roles->mapWithKeys(function (Role $role) use ($counts): array {
            $base = self::baseSlug($role);

            return [$role->getKey() => $counts->get($base) > 1 ? "{$base}--{$role->getKey()}" : $base];
        })->all();
    }

    /**
     * @return array{role: Role|null, allRoles: Collection<int, Role>}
     */
    public static function resolveSlug(string $slug): array
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->get(['id', 'name', 'guard_name']);
        $slugs = self::slugsForRoles($roles, $roles);
        $role = $roles->first(fn (Role $candidate): bool => ($slugs[$candidate->getKey()] ?? null) === $slug);

        return ['role' => $role, 'allRoles' => $roles];
    }

    private static function baseSlug(Role $role): string
    {
        return Str::slug($role->name) ?: 'role';
    }
}
