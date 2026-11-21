<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    protected ?string $role;
    protected string $token;
    protected $organization;

    public function __construct($resource, ?string $role, $token, $organization = null)
    {
        parent::__construct($resource);

        $this->role = $role;
        $this->token = $token;
        $this->organization = $organization;
    }

    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Email verified successfully',

            'user' => new UserResource($this),

            'role' => $this->role,
            'organization' => $this->when(
                $this->organization !== null,
                fn () => new OrganizationResource($this->organization)
            ),

            'token' => $this->token,
        ];
    }
}
