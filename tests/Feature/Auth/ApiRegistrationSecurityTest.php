<?php

namespace Tests\Feature\Auth;

use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ApiRegistrationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_registration_discards_role_permission_and_organization_injection(): void
    {
        Event::fake();
        $storedRegistration = null;
        Redis::shouldReceive('setex')
            ->once()
            ->withArgs(function ($key, $seconds, $value) use (&$storedRegistration) {
                $storedRegistration = $value;

                return $key === 'register:member@example.test' && $seconds === 3600;
            });

        $this->postJson('/api/auth/register', [
            'first_name' => 'New',
            'middle_name' => null,
            'last_name' => 'Member',
            'email' => 'member@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
            'role' => 'super-admin',
            'roles' => ['super-admin'],
            'permissions' => ['manage users'],
            'organization_id' => 999,
        ])->assertOk()
            ->assertJsonPath('email', 'member@example.test');

        $this->assertIsString($storedRegistration);
        $data = json_decode($storedRegistration, true);
        $this->assertArrayNotHasKey('role', $data);
        $this->assertArrayNotHasKey('roles', $data);
        $this->assertArrayNotHasKey('permissions', $data);
        $this->assertArrayNotHasKey('organization_id', $data);

        Redis::shouldReceive('get')
            ->once()
            ->with('register:member@example.test')
            ->andReturn($storedRegistration);
        Redis::shouldReceive('del')
            ->once()
            ->with('register:member@example.test')
            ->andReturn(1);

        $registration = app(RegistrationService::class)
            ->verifyCode('member@example.test', $data['code']);
        $user = $registration['user'];

        $this->assertFalse($user->hasAnyRole());
        $this->assertTrue($user->getAllPermissions()->isEmpty());
    }
}
