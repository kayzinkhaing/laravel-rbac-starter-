<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_permission(): void
    {
        $permission = Permission::create([
            'name' => 'content.read',
            'guard_name' => 'web',
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => $permission->name,
            'guard_name' => 'web',
        ]);
    }

    public function test_it_can_be_assigned_to_multiple_roles(): void
    {
        $permission = Permission::create([
            'name' => 'content.read',
            'guard_name' => 'web',
        ]);
        $reader = Role::create(['name' => 'reader', 'guard_name' => 'web']);
        $editor = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $reader->givePermissionTo($permission);
        $editor->givePermissionTo($permission);

        $this->assertTrue($reader->hasPermissionTo($permission));
        $this->assertTrue($editor->hasPermissionTo($permission));
        $this->assertCount(2, $permission->roles);
    }
}
