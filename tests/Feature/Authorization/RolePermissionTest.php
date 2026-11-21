<?php

namespace Tests\Feature\Authorization;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_have_multiple_roles_and_inherit_their_permissions(): void
    {
        $readPermission = Permission::create([
            'name' => 'content.read',
            'guard_name' => 'web',
        ]);
        $writePermission = Permission::create([
            'name' => 'content.write',
            'guard_name' => 'web',
        ]);

        $reader = Role::create(['name' => 'reader', 'guard_name' => 'web']);
        $writer = Role::create(['name' => 'writer', 'guard_name' => 'web']);
        $reader->givePermissionTo($readPermission, $writePermission);
        $writer->givePermissionTo($writePermission);

        $user = User::factory()->create();
        $user->assignRole($reader, $writer);

        $this->assertCount(2, $reader->permissions);
        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->can('content.read'));
        $this->assertTrue($user->can('content.write'));
    }

    public function test_spatie_permission_middleware_allows_granted_access_and_denies_others(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'permission-manager', 'guard_name' => 'web']);

        $this->actingAs($user)
            ->get($this->roleRoute('role.permissions.index', 'permission-manager'))
            ->assertForbidden();

        $permission = Permission::create([
            'name' => 'manage permissions',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->get($this->roleRoute('role.permissions.index', 'permission-manager'))->assertOk();
        $this->get($this->roleRoute('role.permissions.create', 'permission-manager'))->assertOk();

        $permissionToEdit = Permission::create([
            'name' => 'content.review',
            'guard_name' => 'web',
        ]);
        $this->get($this->roleRoute('role.permissions.edit', 'permission-manager', ['permission' => $permissionToEdit]))->assertOk();
    }

    public function test_can_middleware_and_policy_protect_role_management(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);

        $this->actingAs($user)
            ->get($this->roleRoute('role.roles.index', 'role-manager'))
            ->assertForbidden();

        $permission = Permission::create([
            'name' => 'manage roles',
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->get($this->roleRoute('role.roles.index', 'role-manager'))->assertOk();
        $this->get($this->roleRoute('role.roles.create', 'role-manager'))->assertOk();
    }

    public function test_role_management_creates_and_syncs_permissions(): void
    {
        $manageRoles = Permission::create([
            'name' => 'manage roles',
            'guard_name' => 'web',
        ]);
        $contentPermission = Permission::create([
            'name' => 'content.publish',
            'guard_name' => 'web',
        ]);
        $managerRole = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo($manageRoles, $contentPermission);
        $user = User::factory()->create();
        $user->assignRole($managerRole);

        $this->actingAs($user)
            ->post($this->roleRoute('role.roles.store', 'role-manager'), [
                'name' => 'publisher',
                'permissions' => [(string) $contentPermission->id],
            ])
            ->assertRedirect($this->roleRoute('role.roles.index', 'role-manager'));

        $role = Role::query()->where('name', 'publisher')->firstOrFail();
        $this->get($this->roleRoute('role.roles.edit', 'role-manager', ['role' => $role]))->assertOk();
        $this->assertTrue($role->hasPermissionTo($contentPermission));

        $this->put($this->roleRoute('role.roles.update', 'role-manager', ['role' => $role]), [
            'name' => 'publisher',
            'permissions' => [],
        ])->assertRedirect($this->roleRoute('role.roles.index', 'role-manager'));

        $this->assertCount(0, $role->fresh()->permissions);
    }

    public function test_role_management_rejects_permission_ids_from_another_guard(): void
    {
        $manageRoles = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $apiPermission = Permission::create(['name' => 'api.manage', 'guard_name' => 'api']);
        $managerRole = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo($manageRoles);
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);

        $this->actingAs($manager)
            ->post($this->roleRoute('role.roles.store', 'role-manager'), [
                'name' => 'invalid-guard-role',
                'permissions' => [(string) $apiPermission->id],
            ])
            ->assertSessionHasErrors('permissions');

        $this->assertDatabaseMissing('roles', [
            'name' => 'invalid-guard-role',
            'guard_name' => 'web',
        ]);
    }

    public function test_role_manager_cannot_grant_a_permission_they_do_not_have(): void
    {
        $manageRoles = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $manageUsers = Permission::create(['name' => 'manage users', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo($manageRoles);
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);

        $this->actingAs($manager)
            ->post($this->roleRoute('role.roles.store', 'role-manager'), [
                'name' => 'escalated',
                'permissions' => [$manageUsers->id],
            ])
            ->assertSessionHasErrors('permissions');

        $this->assertDatabaseMissing('roles', ['name' => 'escalated', 'guard_name' => 'web']);
    }

    public function test_role_manager_cannot_edit_or_delete_roles_with_unheld_permissions(): void
    {
        $manageRoles = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $manageUsers = Permission::create(['name' => 'manage users', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo($manageRoles);
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);
        $protectedRole = Role::create(['name' => 'user-manager', 'guard_name' => 'web']);
        $protectedRole->givePermissionTo($manageUsers);

        $this->actingAs($manager)
            ->get($this->roleRoute('role.roles.edit', 'role-manager', ['role' => $protectedRole]))
            ->assertForbidden();
        $this->delete($this->roleRoute('role.roles.destroy', 'role-manager', ['role' => $protectedRole]))
            ->assertForbidden();
        $this->assertDatabaseHas('roles', ['id' => $protectedRole->id]);
    }

    public function test_user_manager_cannot_assign_roles_with_unheld_permissions(): void
    {
        $manageUsers = Permission::create(['name' => 'manage users', 'guard_name' => 'web']);
        $manageRoles = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'user-manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo($manageUsers);
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);
        $elevatedRole = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $elevatedRole->givePermissionTo($manageRoles);

        $this->actingAs($manager)
            ->post($this->roleRoute('role.users.store', 'user-manager'), [
                'first_name' => 'New',
                'last_name' => 'Administrator',
                'email' => 'new-admin@example.test',
                'password' => 'password-123',
                'password_confirmation' => 'password-123',
                'roles' => [$elevatedRole->id],
            ])
            ->assertSessionHasErrors('roles');

        $this->assertDatabaseMissing('users', ['email' => 'new-admin@example.test']);
    }

    public function test_user_manager_cannot_update_an_account_with_unheld_permissions(): void
    {
        $manageUsers = Permission::create(['name' => 'manage users', 'guard_name' => 'web']);
        $manageRoles = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'user-manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo($manageUsers);
        $manager = User::factory()->create();
        $manager->assignRole($managerRole);
        $protectedRole = Role::create(['name' => 'role-manager', 'guard_name' => 'web']);
        $protectedRole->givePermissionTo($manageRoles);
        $target = User::factory()->create();
        $target->assignRole($protectedRole);

        $this->actingAs($manager)
            ->get($this->roleRoute('role.users.edit', 'user-manager', ['user' => $target]))
            ->assertForbidden();
        $this->put($this->roleRoute('role.users.update', 'user-manager', ['user' => $target]), [
            'first_name' => $target->first_name,
            'last_name' => $target->last_name,
            'email' => $target->email,
            'password' => '',
            'password_confirmation' => '',
        ])->assertForbidden();
    }

    public function test_permission_manager_cannot_rename_a_permission_to_an_administrative_capability(): void
    {
        $managePermissions = Permission::create(['name' => 'manage permissions', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'permission-manager', 'guard_name' => 'web']);
        $role->givePermissionTo($managePermissions);
        $manager = User::factory()->create();
        $manager->assignRole($role);
        $ordinaryPermission = Permission::create(['name' => 'content.review', 'guard_name' => 'web']);

        $this->actingAs($manager)
            ->put($this->roleRoute('role.permissions.update', 'permission-manager', ['permission' => $ordinaryPermission]), ['name' => 'manage users'])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseHas('permissions', ['id' => $ordinaryPermission->id, 'name' => 'content.review']);
    }

    public function test_permission_changes_refresh_the_permission_registrar_cache(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->getPermissions();

        $permission = Permission::create([
            'name' => 'cache.refresh',
            'guard_name' => 'web',
        ]);

        $this->assertTrue(
            $registrar->getPermissions()->contains('name', $permission->name)
        );
    }

    public function test_profile_policy_restricts_access_to_the_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->assertFalse($otherUser->can('view', $owner));
        $this->assertTrue($owner->can('view', $owner));

        $this->actingAs($otherUser)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($otherUser->email);
    }
}
