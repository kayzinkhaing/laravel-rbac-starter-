<?php

namespace Tests\Unit\Models;

use App\Models\Media;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user_using_factory()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function it_can_assign_and_check_roles()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'content-editor', 'guard_name' => 'web']);

        $user->assignRole($role);

        $this->assertTrue($user->hasRole($role));
        $this->assertFalse($user->hasRole('content-reader'));
    }

    /** @test */
    public function it_returns_full_name_accessor()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'middle_name' => 'K',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John K Doe', $user->name);
    }

    /** @test */
    public function it_can_return_profile_image_url()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Media::factory()->for($user, 'mediable')->create([
            'url' => 'avatars/test.png',
        ]);

        Storage::disk('public')->put('avatars/test.png', 'dummy');

        $this->assertStringContainsString(
            'avatars/test.png',
            $user->profileImage()->url
        );
    }

    /** @test */
    public function it_can_check_permissions_through_roles()
    {
        $user = User::factory()->create();

        $role = Role::create(['name' => 'content-editor', 'guard_name' => 'web']);
        $permission = Permission::create([
            'name' => 'content.write',
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->assertTrue($user->can('content.write'));
    }

    /** @test */
    public function it_has_no_roles_when_none_are_assigned()
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->roles);
    }
}
