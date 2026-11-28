<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LocalRbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocalRbacSeederTest extends TestCase
{
    use RefreshDatabase;

    private const STARTER_PERMISSIONS = [
        'manage master data',
        'manage permissions',
        'manage roles',
        'manage system permissions',
        'manage users',
    ];

    public function test_local_seeder_is_repeatable_and_preserves_existing_account_data(): void
    {
        $this->seed(LocalRbacSeeder::class);

        $admin = User::query()
            ->where('email', 'admin.rbac1331@gmail.com')
            ->firstOrFail();

        $member = User::query()
            ->where('email', 'member.rbac1331@gmail.com')
            ->firstOrFail();

        $admin->forceFill([
            'password' => Hash::make('changed-local-password'),
        ])->save();

        $extraRole = Role::factory()->create([
            'name' => 'extra-local-role',
            'guard_name' => 'web',
        ]);

        $member->assignRole($extraRole);

        $this->seed(LocalRbacSeeder::class);

        $this->assertSame(5, User::query()->count());
        $this->assertSame(5, Role::query()->count());
        $this->assertSame(5, Permission::query()->count());

        $this->assertTrue(
            Hash::check('changed-local-password', $admin->fresh()->password)
        );

        $this->assertTrue($member->fresh()->hasRole($extraRole));
    }

    public function test_database_seeder_does_not_seed_outside_local_or_testing(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->app->make(DatabaseSeeder::class)->run();

        $this->assertSame(0, User::query()->count());
        $this->assertSame(0, Role::query()->count());
        $this->assertSame(0, Permission::query()->count());
    }

    public function test_local_seeder_preserves_existing_user_role_assignment_and_updates_super_admin_permissions(): void
    {
        $manageUsers = Permission::create([
            'name' => 'manage users',
            'guard_name' => 'web',
        ]);

        $existingRole = Role::create([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $existingRole->givePermissionTo($manageUsers);

        $existingUser = User::factory()->create([
            'email' => 'admin.rbac1331@gmail.com',
        ]);

        $this->seed(LocalRbacSeeder::class);

        $this->assertFalse($existingUser->fresh()->hasAnyRole());

        $actualPermissions = $existingRole->fresh()
            ->permissions
            ->pluck('name')
            ->sort()
            ->values()
            ->all();

        $this->assertSame(self::STARTER_PERMISSIONS, $actualPermissions);
    }

    public function test_model_factories_create_role_permission_and_user_role_assignments(): void
    {
        $permission = Permission::factory()->create();

        $role = Role::factory()
            ->withPermissions($permission)
            ->create();

        $user = User::factory()
            ->withRoles($role)
            ->create();

        $this->assertTrue($role->hasPermissionTo($permission));
        $this->assertTrue($user->hasRole($role));
    }

    public function test_seeded_accounts_authenticate_and_land_in_their_role_appropriate_area(): void
    {
        $this->seed(LocalRbacSeeder::class);

        $accounts = [
            'admin.rbac1331@gmail.com' => [
                '/super-admin/dashboard',
                ['users.index', 'roles.index', 'permissions.index'],
                [],
            ],
            'users.rbac1331@gmail.com' => [
                '/user-administrator/dashboard',
                ['users.index'],
                ['roles.index', 'permissions.index'],
            ],
            'roles.rbac1331@gmail.com' => [
                '/role-administrator/dashboard',
                ['roles.index'],
                ['users.index', 'permissions.index'],
            ],
            'permissions.rbac1331@gmail.com' => [
                '/permission-administrator/dashboard',
                ['permissions.index'],
                ['users.index', 'roles.index'],
            ],
            'member.rbac1331@gmail.com' => [
                'home',
                ['profile.edit'],
                ['dashboard', 'users.index', 'roles.index', 'permissions.index'],
            ],
        ];

        foreach ($accounts as $email => [$destination, $allowedRoutes, $deniedRoutes]) {
            $redirectTo = $destination === 'home'
                ? route('home', absolute: false)
                : $destination;

            $this->post(route('login'), [
                'email' => $email,
                'password' => LocalRbacSeeder::PASSWORD,
            ])->assertRedirect($redirectTo);

            $this->assertAuthenticated();

            foreach ($allowedRoutes as $routeName) {
                $this->get(route($routeName))->assertOk();
            }

            foreach ($deniedRoutes as $routeName) {
                $this->get(route($routeName))->assertForbidden();
            }

            $this->post(route('logout'));

            $this->assertGuest();
        }
    }

    public function test_seeded_accounts_have_exact_role_and_permission_assignments(): void
    {
        $this->seed(LocalRbacSeeder::class);

        $expected = [
            'admin.rbac1331@gmail.com' => [
                'super-admin',
                self::STARTER_PERMISSIONS,
            ],
            'users.rbac1331@gmail.com' => [
                'user-administrator',
                ['manage users'],
            ],
            'roles.rbac1331@gmail.com' => [
                'role-administrator',
                ['manage roles'],
            ],
            'permissions.rbac1331@gmail.com' => [
                'permission-administrator',
                ['manage permissions'],
            ],
            'member.rbac1331@gmail.com' => [
                null,
                [],
            ],
        ];

        foreach ($expected as $email => [$expectedRole, $expectedPermissions]) {
            $user = User::query()
                ->where('email', $email)
                ->firstOrFail();

            $actualRoles = $user->getRoleNames()
                ->sort()
                ->values()
                ->all();

            $actualPermissions = $user->getAllPermissions()
                ->pluck('name')
                ->sort()
                ->values()
                ->all();

            sort($expectedPermissions);

            $this->assertSame(
                $expectedRole === null ? [] : [$expectedRole],
                $actualRoles,
                "Unexpected roles for {$email}"
            );

            $this->assertSame(
                $expectedPermissions,
                $actualPermissions,
                "Unexpected permissions for {$email}"
            );
        }
    }
}
