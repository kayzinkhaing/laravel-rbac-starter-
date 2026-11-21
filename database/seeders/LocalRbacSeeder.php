<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalRbacSeeder extends Seeder
{
    public const PASSWORD = 'LocalOnly!2026';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $permissions = [];

        foreach (AdminDashboardAccess::PERMISSIONS as $name) {
            $permissions[$name] = Permission::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $roleDefinitions = [
            'super-admin' => AdminDashboardAccess::PERMISSIONS,
            'user-administrator' => ['manage users'],
            'role-administrator' => ['manage roles'],
            'permission-administrator' => ['manage permissions'],
        ];
        $roles = [];
        $createdRoles = [];

        foreach ($roleDefinitions as $name => $permissionNames) {
            $role = Role::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);

            $createdRoles[$name] = $role->wasRecentlyCreated;

            if ($createdRoles[$name]) {
                foreach ($permissionNames as $permissionName) {
                    $role->givePermissionTo($permissions[$permissionName]);
                }
            }

            $roles[$name] = $role;
        }

        $accounts = [
            ['admin.rbac1331@gmail.com', 'Local', 'Administrator', 'super-admin'],
            ['users.rbac1331@gmail.com', 'User', 'Administrator', 'user-administrator'],
            ['roles.rbac1331@gmail.com', 'Role', 'Administrator', 'role-administrator'],
            ['permissions.rbac1331@gmail.com', 'Permission', 'Administrator', 'permission-administrator'],
            ['member.rbac1331@gmail.com', 'Sample', 'Member', null],
        ];

        foreach ($accounts as [$email, $firstName, $lastName, $roleName]) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => Hash::make(self::PASSWORD),
                    'email_verified_at' => now(),
                ]
            );

            if ($roleName !== null && $user->wasRecentlyCreated && $createdRoles[$roleName]) {
                $user->assignRole($roles[$roleName]);
            }
        }
    }
}
