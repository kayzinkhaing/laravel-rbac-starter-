<?php

namespace Tests\Unit\Http\Controllers\Web;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTestClean extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $manageRoles = Permission::create([
            'name' => 'manage roles',
            'guard_name' => 'web',
        ]);
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo($manageRoles);

        $this->manager = User::factory()->create();
        $this->manager->assignRole($adminRole);
    }

    public function test_environment_is_testing(): void
    {
        $this->assertTrue(app()->environment('testing'));
    }

    public function test_index_uses_the_authenticated_request_and_renders_roles(): void
    {
        $this->actingAs($this->manager)
            ->get($this->roleRoute('role.roles.index', 'admin'))
            ->assertOk()
            ->assertViewHas('roles', function ($roles): bool {
                return $roles->getCollection()->pluck('name')->contains('admin');
            });
    }

    public function test_store_requires_the_role_permission_and_creates_a_role(): void
    {
        $this->actingAs($this->manager)
            ->post($this->roleRoute('role.roles.store', 'admin'), [
                'name' => 'user',
            ])
            ->assertRedirect($this->roleRoute('role.roles.index', 'admin'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'name' => 'user',
            'guard_name' => 'web',
        ]);
    }

    public function test_users_without_role_management_permission_are_forbidden(): void
    {
        $viewerRole = Role::create([
            'name' => 'viewer',
            'guard_name' => 'web',
        ]);
        $viewer = User::factory()->create();
        $viewer->assignRole($viewerRole);

        $this->actingAs($viewer)
            ->get($this->roleRoute('role.roles.index', 'viewer'))
            ->assertForbidden();

        $this->post($this->roleRoute('role.roles.store', 'viewer'), [
            'name' => 'unauthorized-role',
        ])->assertForbidden();

        $this->assertDatabaseMissing('roles', [
            'name' => 'unauthorized-role',
            'guard_name' => 'web',
        ]);
    }

    public function test_update_uses_the_prefixed_route_and_updates_the_bound_role(): void
    {
        $role = Role::create([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        $this->actingAs($this->manager)
            ->put($this->roleRoute('role.roles.update', 'admin', ['role' => $role]), [
                'name' => 'content-editor',
                'permissions' => [],
            ])
            ->assertRedirect($this->roleRoute('role.roles.index', 'admin'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'content-editor',
            'guard_name' => 'web',
        ]);
    }

    public function test_delete_checks_authorization_and_removes_the_bound_role(): void
    {
        $role = Role::create([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        $this->actingAs($this->manager)
            ->delete($this->roleRoute('role.roles.destroy', 'admin', ['role' => $role]))
            ->assertRedirect($this->roleRoute('role.roles.index', 'admin'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
