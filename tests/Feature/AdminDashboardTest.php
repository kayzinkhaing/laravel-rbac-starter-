<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_renders_the_public_homepage_for_guests_and_regular_users(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('User management and access control')
            ->assertSee('Sign in');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/')->assertOk()->assertSee('My profile');
        $this->get(route('dashboard'))->assertForbidden();
    }

    public function test_dashboard_renders_database_backed_totals_and_the_recent_registration_panel(): void
    {
        $user = $this->createAdminUser();
        Role::create(['name' => 'editor', 'guard_name' => 'web']);
        Permission::create(['name' => 'content.edit', 'guard_name' => 'web']);
        $registeredUser = User::factory()->create();

        $this->actingAs($user)
            ->get(route('role.dashboard', ['roleSlug' => 'super-admin']))
            ->assertOk()
            ->assertSee('Total users')
            ->assertSee('Verified accounts')
            ->assertSee('Roles')
            ->assertSee('Permissions')
            ->assertSee('Recent registrations')
            ->assertSee('Administrative activity')
            ->assertSee($registeredUser->email);
    }

    public function test_dashboard_only_shows_statistics_allowed_by_the_users_permissions(): void
    {
        $permission = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'role-administrator', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('role.dashboard', ['roleSlug' => 'role-administrator']))
            ->assertOk()
            ->assertSee('Roles')
            ->assertDontSee('Total users')
            ->assertDontSee('Recent registrations');
    }

    public function test_user_management_requires_its_permission_and_assigns_roles(): void
    {
        $manager = $this->createUserManager();
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $this->actingAs(User::factory()->create())
            ->get('/super-admin/users')
            ->assertForbidden();

        $this->actingAs($manager)
            ->get($this->roleRoute('role.users.create', 'user-manager'))
            ->assertOk()
            ->assertSee('Role assignment');

        $this->actingAs($manager)
            ->post($this->roleRoute('role.users.store', 'user-manager'), [
                'first_name' => 'Riley',
                'middle_name' => '',
                'last_name' => 'Taylor',
                'email' => 'riley@example.com',
                'password' => 'password-123',
                'password_confirmation' => 'password-123',
                'roles_present' => '1',
                'roles' => [$role->id],
            ])
            ->assertRedirect($this->roleRoute('role.users.index', 'user-manager'))
            ->assertSessionHasNoErrors();

        $created = User::query()->where('email', 'riley@example.com')->firstOrFail();
        $this->assertTrue($created->hasRole($role));

        $this->actingAs($manager)
            ->get($this->roleRoute('role.users.show', 'user-manager', ['user' => $created]))
            ->assertOk()
            ->assertSee('Effective permissions')
            ->assertSee('Assigned roles');

        $this->get($this->roleRoute('role.users.edit', 'user-manager', ['user' => $created]))
            ->assertOk()
            ->assertSee('Save changes');
    }

    public function test_user_role_assignments_can_be_cleared(): void
    {
        $manager = $this->createUserManager();
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($manager)
            ->put($this->roleRoute('role.users.update', 'user-manager', ['user' => $user]), [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => '',
                'password_confirmation' => '',
                'roles_present' => '1',
            ])
            ->assertRedirect($this->roleRoute('role.users.show', 'user-manager', ['user' => $user]))
            ->assertSessionHasNoErrors();

        $this->assertSame([], $user->fresh()->roles()->pluck('id')->all());
    }

    public function test_role_dashboard_requires_the_matching_assigned_role_and_selects_roles_explicitly(): void
    {
        $user = $this->createAdminUser();
        $secondRole = Role::create(['name' => 'user-administrator', 'guard_name' => 'web']);
        $user->assignRole($secondRole);

        $this->actingAs($user)
            ->get('/super-admin/dashboard')
            ->assertOk()
            ->assertSee('Active role');

        $this->get('/user-administrator/dashboard')->assertForbidden();
        $this->post(route('active-role.update'), ['role_id' => $secondRole->id])
            ->assertRedirect('/user-administrator/dashboard');
        $this->get('/user-administrator/dashboard')->assertOk();
        Role::create(['name' => 'restricted-role', 'guard_name' => 'web']);
        $this->get('/restricted-role/dashboard')->assertForbidden();
        $duplicateRoleA = Role::create(['name' => 'duplicate slug', 'guard_name' => 'web']);
        $duplicateRoleB = Role::create(['name' => 'duplicate-slug', 'guard_name' => 'web']);
        $user->assignRole([$duplicateRoleA, $duplicateRoleB]);
        $slugA = AdminDashboardAccess::slugFor($duplicateRoleA);
        $slugB = AdminDashboardAccess::slugFor($duplicateRoleB);
        $this->assertNotSame($slugA, $slugB);
        $this->get(route('role.dashboard', ['roleSlug' => $slugA]))->assertForbidden();
        $this->post(route('active-role.update'), ['role_id' => $duplicateRoleA->id])
            ->assertRedirect(route('role.dashboard', ['roleSlug' => $slugA]));
        $this->get(route('role.dashboard', ['roleSlug' => $slugA]))->assertOk();
        $this->post(route('active-role.update'), ['role_id' => $duplicateRoleB->id])
            ->assertRedirect(route('role.dashboard', ['roleSlug' => $slugB]));
        $this->get(route('role.dashboard', ['roleSlug' => $slugB]))->assertOk();
        $this->get('/unknown-role/dashboard')->assertNotFound();

        $this->get(route('dashboard'))
            ->assertRedirect(route('role.dashboard', ['roleSlug' => $slugB]));
    }

    public function test_role_profile_routes_are_assignment_bound_and_use_the_role_dashboard_layout(): void
    {
        $role = Role::create(['name' => 'profile-administrator', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);
        $otherRole = Role::create(['name' => 'other-administrator', 'guard_name' => 'web']);

        $this->actingAs($user)
            ->get('/profile-administrator/profile')
            ->assertOk()
            ->assertSee('Workspace')
            ->assertSee('Account')
            ->assertSee($user->email)
            ->assertSee('href="'.route('role.twofactor.setup', ['roleSlug' => 'profile-administrator']).'"', false);

        $this->get('/profile')
            ->assertStatus(307)
            ->assertRedirect('/profile-administrator/profile');

        $this->patch('/profile-administrator/profile', [
            'name' => 'Updated Role Profile',
            'email' => $user->email,
        ])->assertRedirect('/profile-administrator/profile');
        $this->assertSame('Updated Role Profile', $user->fresh()->name);

        $this->get('/other-administrator/profile')->assertForbidden();
    }

    public function test_super_admin_and_staff_management_pages_use_their_role_prefixes(): void
    {
        $superAdmin = $this->createAdminUser();
        $superAdminRoute = fn (string $name, array $parameters = []): string => $this->roleRoute(
            "role.{$name}",
            'super-admin',
            $parameters
        );

        $this->actingAs($superAdmin)
            ->get('/super-admin/dashboard')
            ->assertOk()
            ->assertSee('href="'.$superAdminRoute('users.index').'"', false)
            ->assertSee('href="'.$superAdminRoute('roles.index').'"', false)
            ->assertSee('href="'.$superAdminRoute('permissions.index').'"', false);

        $this->get($superAdminRoute('users.index'))->assertOk();
        $this->get($superAdminRoute('roles.index'))->assertOk();
        $this->get($superAdminRoute('permissions.index'))->assertOk();
        $this->get('/roles')->assertStatus(307)->assertRedirect('/super-admin/roles');

        $manageUsers = Permission::query()
            ->where('name', 'manage users')
            ->where('guard_name', 'web')
            ->firstOrFail();
        $staffRole = Role::create(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->givePermissionTo($manageUsers);
        $staff = User::factory()->create();
        $staff->assignRole($staffRole);
        Role::create(['name' => 'another-role', 'guard_name' => 'web']);

        $this->actingAs($staff)
            ->get('/staff/users')
            ->assertOk()
            ->assertSee('href="'.$this->roleRoute('role.users.index', 'staff').'"', false);

        $this->get('/another-role/users')->assertForbidden();
    }

    public function test_live_tables_search_and_combine_the_supported_filters(): void
    {
        $manager = $this->createUserManager();
        $role = Role::create(['name' => 'support-operator', 'guard_name' => 'web']);
        $matched = User::factory()->create([
            'first_name' => 'Jordan',
            'last_name' => 'Searchable',
            'email' => 'jordan.searchable@example.com',
            'email_verified_at' => now(),
        ]);
        $matched->assignRole($role);
        $alsoMatched = User::factory()->create([
            'first_name' => 'Avery',
            'email' => 'aaron.searchable@example.com',
            'email_verified_at' => now(),
        ]);
        $alsoMatched->assignRole($role);
        User::factory()->create([
            'first_name' => 'Jordan',
            'email' => 'jordan.unverified@example.com',
            'email_verified_at' => null,
        ]);

        $this->actingAs($manager)
            ->get($this->roleRoute('role.users.index', 'user-manager', [
                'search' => 'support',
                'role' => $role->id,
                'verified' => 'verified',
                'sort' => 'email',
                'direction' => 'asc',
            ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSeeInOrder(['aaron.searchable@example.com', 'jordan.searchable@example.com'])
            ->assertSee('jordan.searchable@example.com')
            ->assertDontSee('jordan.unverified@example.com')
            ->assertSee('Showing 1–2 of 2');
    }

    public function test_role_and_permission_tables_support_ajax_search_and_sorting(): void
    {
        $roleAdmin = $this->createRoleManager();
        Role::create(['name' => 'editor-role', 'guard_name' => 'web']);
        Role::create(['name' => 'editor-alpha', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.export', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.import', 'guard_name' => 'web']);

        $this->actingAs($roleAdmin)
            ->get($this->roleRoute('role.roles.index', 'role-manager', ['search' => 'editor', 'sort' => 'name', 'direction' => 'desc']), [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->assertOk()
            ->assertSeeInOrder(['editor-role', 'editor-alpha'])
            ->assertDontSee('user-manager');

        $permissionAdmin = $this->createPermissionManager();
        $this->actingAs($permissionAdmin)
            ->get($this->roleRoute('role.permissions.index', 'permission-manager', ['search' => 'users.', 'sort' => 'name', 'direction' => 'desc']), [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->assertOk()
            ->assertSeeInOrder(['users.import', 'users.export'])
            ->assertDontSee('roles.manage');
    }

    public function test_user_listing_query_count_does_not_grow_with_result_count(): void
    {
        $manager = $this->createUserManager();
        User::factory()->create(['email' => 'one.result@example.com']);
        foreach (range(1, 18) as $index) {
            User::factory()->create(['email' => "batch{$index}@example.com"]);
        }

        $this->actingAs($manager)
            ->get($this->roleRoute('role.users.index', 'user-manager', ['search' => 'warmup']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $selectCount = 0;
        DB::listen(function (QueryExecuted $query) use (&$selectCount): void {
            if (str_starts_with(strtolower(ltrim($query->sql)), 'select')) {
                $selectCount++;
            }
        });

        $this->actingAs($manager)
            ->get($this->roleRoute('role.users.index', 'user-manager', ['search' => 'one.result']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();
        $singleResultQueryCount = $selectCount;

        $selectCount = 0;
        $this->get($this->roleRoute('role.users.index', 'user-manager', ['search' => 'batch']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $this->assertLessThanOrEqual(1, abs($singleResultQueryCount - $selectCount));

        $this->get($this->roleRoute('role.users.index', 'user-manager', ['search' => 'batch', 'page' => 2]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertSee('Showing 16–18 of 18');
    }

    private function createUserManager(): User
    {
        $permission = Permission::create([
            'name' => 'manage users',
            'guard_name' => 'web',
        ]);
        $role = Role::create([
            'name' => 'user-manager',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permission);

        $manager = User::factory()->create();
        $manager->assignRole($role);

        return $manager;
    }

    private function createRoleManager(): User
    {
        $permission = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createPermissionManager(): User
    {
        $permission = Permission::create(['name' => 'manage permissions', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'permission-manager', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createAdminUser(): User
    {
        $permissions = collect([
            'manage users',
            'manage roles',
            'manage permissions',
        ])->map(fn (string $name) => Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]));
        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
