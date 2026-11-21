<?php

namespace Tests\Feature\Authorization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_endpoints_require_an_authenticated_api_user(): void
    {
        $this->getJson('/api/v1/organizations')->assertUnauthorized();
        $this->getJson('/api/v1/organizations/1')->assertUnauthorized();
    }

    public function test_regular_api_user_cannot_list_or_view_another_organization(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('organization_id');
            $table->timestamps();
        });
        $organization = Organization::create(['name' => 'Private organization']);
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/organizations')
            ->assertForbidden();

        $this->getJson('/api/v1/organizations/'.$organization->id)->assertForbidden();
    }

    public function test_client_profile_creation_requires_the_management_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/api/v1/client-profiles', [
                'user_id' => $user->id,
                'organization_id' => 1,
            ])
            ->assertForbidden();
    }
}
