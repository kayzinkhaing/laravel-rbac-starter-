<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
        Notification::assertSentTo(User::first(), VerifyEmail::class);
    }

    public function test_public_registration_cannot_assign_roles_or_permissions(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'New Member',
            'email' => 'member@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'super-admin',
            'roles' => ['super-admin'],
            'permissions' => ['manage users', 'manage roles', 'manage permissions'],
        ])->assertRedirect(route('home', absolute: false));

        $user = User::query()->where('email', 'member@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasAnyRole());
        $this->assertTrue($user->getAllPermissions()->isEmpty());
    }
}
