<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_profile_image_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('private');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.image.update'), [
                'image' => UploadedFile::fake()->image('avatar.jpg', 300, 300),
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'profile-image-updated');

        $firstPath = $user->fresh()->profileImage()->url;
        Storage::disk('private')->assertExists($firstPath);
        $this->get(route('profile.image', $user))->assertOk();
        $this->get(route('profile.image', $otherUser))->assertForbidden();

        $this->post(route('profile.image.update'), [
            'image' => UploadedFile::fake()->image('replacement.jpg', 300, 300),
        ])->assertRedirect(route('profile.edit'));

        $replacementPath = $user->fresh()->profileImage()->url;
        $this->assertNotSame($firstPath, $replacementPath);
        Storage::disk('private')->assertMissing($firstPath);
        Storage::disk('private')->assertExists($replacementPath);

        $this->actingAs($user)
            ->delete(route('profile.image.delete'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'profile-image-removed');

        $this->assertNull($user->fresh()->profileImage());
        Storage::disk('private')->assertMissing($replacementPath);
    }

    public function test_profile_image_upload_rejects_files_over_the_product_limit(): void
    {
        Storage::fake('private');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('profile.image.update'), [
                'image' => UploadedFile::fake()->image('oversized.jpg')->size(2049),
            ])
            ->assertSessionHasErrors('image');

        $this->assertNull($user->fresh()->profileImage());
    }

    public function test_regular_users_cannot_edit_another_users_administrative_record(): void
    {
        $actor = User::factory()->create();
        $otherUser = User::factory()->create();
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

        $this->actingAs($actor)
            ->get($this->roleRoute('role.users.edit', 'super-admin', ['user' => $otherUser]))
            ->assertForbidden();

        $this->put($this->roleRoute('role.users.update', 'super-admin', ['user' => $otherUser]), [
            'name' => 'Changed Without Access',
            'email' => $otherUser->email,
        ])->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'first_name' => $otherUser->first_name,
            'last_name' => $otherUser->last_name,
        ]);
    }

    public function test_profile_update_ignores_attempts_to_change_roles_permissions_or_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $manageUsers = Permission::create(['name' => 'manage users', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'user-administrator', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageUsers);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Profile',
                'email' => $user->email,
                'user_id' => $otherUser->id,
                'roles' => [$adminRole->id],
                'permissions' => [$manageUsers->id],
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Updated Profile', $user->fresh()->name);
        $this->assertFalse($user->fresh()->hasRole($adminRole));
        $this->assertFalse($user->fresh()->hasPermissionTo($manageUsers));
        $this->assertNotSame('Updated Profile', $otherUser->fresh()->name);
    }
}
