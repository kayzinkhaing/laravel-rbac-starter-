<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Services\ClientProfiles;
use Illuminate\Http\Request;

class ClientProfileController extends Controller
{
    protected $organizations;

    public function __construct(ClientProfiles $organizations)
    {
        $this->organizations = $organizations;

        // Parent Controller Constructor
        parent::__construct(
            $this->organizations
        );
    }

    public function store(Request $request)
    {
        $this->authorize('create', ClientProfile::class);

        return parent::store($request);
    }
}
