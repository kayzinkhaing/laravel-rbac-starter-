<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_role(): void
    {
        $role = Role::create(['name' => 'content-reader', 'guard_name' => 'web']);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => 'web',
        ]);
    }

    public function test_it_can_be_assigned_to_a_user(): void
    {
        $role = Role::create(['name' => 'content-reader', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $user->assignRole($role);

        $this->assertTrue($user->hasRole($role));
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);
    }

    public function test_it_can_have_permissions(): void
    {
        $role = Role::create(['name' => 'content-editor', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'content.write', 'guard_name' => 'web']);

        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermissionTo($permission));
        $this->assertTrue($permission->roles->contains($role));
    }

    public function test_assigned_users_inherit_role_permissions(): void
    {
        $role = Role::create(['name' => 'content-reader', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'content.read', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->can('content.read'));
    }
}
