<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminDashboardAccess;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_administrators_are_redirected_to_the_dashboard_after_login(): void
    {
        $permission = Permission::create(['name' => 'manage roles', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'role-administrator', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/role-administrator/dashboard');
    }

    public function test_two_factor_setup_generates_a_qr_code_with_the_configured_renderer(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('twofactor.setup'))
            ->assertOk()
            ->assertSee('Scan with your authenticator app')
            ->assertSee('<img src="data:image/svg+xml;base64,', false)
            ->assertSee('alt="Authenticator setup QR code"', false);

        $this->assertNotEmpty($user->fresh()->google2fa_secret);
    }

    public function test_authenticator_code_can_complete_two_factor_enrollment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('twofactor.setup'))->assertOk();
        $secret = $user->fresh()->google2fa_secret;
        $code = app('pragmarx.google2fa')->getCurrentOtp($secret);

        $this->post(route('twofactor.enable'), ['code' => $code])
            ->assertRedirect(route('twofactor.setup'))
            ->assertSessionHas('success', 'Two-factor authentication enabled.');

        $this->assertTrue($user->fresh()->two_factor_enabled);
        $recoveryCodes = $user->fresh()->two_factor_recovery_codes;
        $this->assertIsArray($recoveryCodes);
        $this->assertCount(10, $recoveryCodes);
    }

    public function test_two_factor_routes_follow_the_active_role_and_reject_cross_role_requests(): void
    {
        $roles = collect(['quality reviewer', 'content manager'])
            ->map(fn (string $name) => Role::create(['name' => $name, 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($roles);
        $slugs = $roles->mapWithKeys(fn (Role $role) => [
            $role->id => AdminDashboardAccess::slugFor($role),
        ]);
        $firstRole = $roles->first();
        $secondRole = $roles->last();
        $firstSlug = $slugs[$firstRole->id];
        $secondSlug = $slugs[$secondRole->id];

        $this->actingAs($user)
            ->withSession(['active_role_id' => $firstRole->id])
            ->get(route('role.twofactor.setup', ['roleSlug' => $firstSlug]))
            ->assertOk()
            ->assertSee('action="'.route('role.twofactor.enable', ['roleSlug' => $firstSlug]).'"', false);

        $this->get(route('role.twofactor.setup', ['roleSlug' => $secondSlug]))->assertForbidden();
        $this->post(route('role.twofactor.enable', ['roleSlug' => $secondSlug]), ['code' => '000000'])
            ->assertForbidden();
        $this->get(route('twofactor.setup'))
            ->assertStatus(307)
            ->assertRedirect(route('role.twofactor.setup', ['roleSlug' => $firstSlug]));
        $this->post(route('twofactor.enable'), ['code' => '000000'])
            ->assertStatus(307)
            ->assertRedirect(route('role.twofactor.enable', ['roleSlug' => $firstSlug]));
        $this->post(route('twofactor.disable'))
            ->assertStatus(307)
            ->assertRedirect(route('role.twofactor.disable', ['roleSlug' => $firstSlug]));

        foreach ($roles as $role) {
            $slug = $slugs[$role->id];

            $this->post(route('active-role.update'), ['role_id' => $role->id])
                ->assertRedirect(route('role.dashboard', ['roleSlug' => $slug]));
            $this->get(route('role.twofactor.setup', ['roleSlug' => $slug]))
                ->assertOk()
                ->assertSee('action="'.route('role.twofactor.enable', ['roleSlug' => $slug]).'"', false);

            $code = app('pragmarx.google2fa')->getCurrentOtp($user->fresh()->google2fa_secret);
            $this->post(route('role.twofactor.enable', ['roleSlug' => $slug]), ['code' => $code])
                ->assertRedirect(route('role.twofactor.setup', ['roleSlug' => $slug]))
                ->assertSessionHas('success', 'Two-factor authentication enabled.');

            $this->assertTrue($user->fresh()->two_factor_enabled);

            $this->post(route('role.twofactor.disable', ['roleSlug' => $slug]))
                ->assertRedirect(route('role.twofactor.setup', ['roleSlug' => $slug]))
                ->assertSessionHas('success', 'Two-factor authentication disabled.');

            $this->assertFalse($user->fresh()->two_factor_enabled);
        }
    }

    public function test_api_two_factor_actions_are_not_redirected_to_role_web_routes(): void
    {
        $role = Role::create(['name' => 'api access', 'guard_name' => 'web']);
        $user = User::factory()->create(['two_factor_enabled' => true]);
        $user->assignRole($role);

        $response = $this->actingAs($user, 'api')
            ->post('/api/v1/two-factor/disable');

        $response->assertStatus(302);
        $this->assertStringNotContainsString(
            '/'.AdminDashboardAccess::slugFor($role).'/two-factor/disable',
            $response->headers->get('Location')
        );
        $this->assertFalse($user->fresh()->two_factor_enabled);
    }

    public function test_recovery_code_migration_repairs_a_missing_column_without_dropping_codes_on_rollback(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_recovery_codes');
        });

        $migration = require database_path('migrations/2026_11_21_000003_add_two_factor_recovery_codes_to_users_table.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumn('users', 'two_factor_recovery_codes'));
        $recoveryCodesColumn = collect(Schema::getColumns('users'))
            ->firstWhere('name', 'two_factor_recovery_codes');
        $this->assertTrue($recoveryCodesColumn['nullable']);

        $user = User::factory()->create();
        $user->two_factor_recovery_codes = ['hashed-code'];
        $user->save();

        $migration->down();

        $this->assertSame(['hashed-code'], $user->fresh()->two_factor_recovery_codes);
    }

    public function test_users_with_two_factor_enabled_must_verify_before_authentication(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'google2fa_secret' => 'test-secret',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('twofactor.verify'));

        $this->assertGuest();
        $this->get(route('twofactor.verify'))
            ->assertOk()
            ->assertSessionHas('2fa:web:user:id', $user->id);
    }

    public function test_successful_two_factor_verification_completes_web_login(): void
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'google2fa_secret' => 'test-secret',
        ]);
        $google2fa = \Mockery::mock();
        $google2fa->shouldReceive('verifyKey')->once()->with('test-secret', '123456')->andReturnTrue();
        $this->app->instance('pragmarx.google2fa', $google2fa);

        $response = $this->withSession(['2fa:web:user:id' => $user->id])
            ->post(route('twofactor.verify.post'), ['code' => '123456'])
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $response->assertSessionMissing('2fa:web:user:id');
        $this->assertArrayNotHasKey('google2fa_secret', $user->toArray());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_requires_a_verified_email(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_profile_routes_require_authentication(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }
}
