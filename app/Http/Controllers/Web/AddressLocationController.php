<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\State;
use App\Models\Township;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;

class AddressLocationController extends Controller
{
    public function states(int $country): JsonResponse
    {
        return response()->json(State::where('country_id', $country)->orderBy('name')->get(['id', 'name', 'name_local']));
    }

    public function districts(int $state): JsonResponse
    {
        return response()->json(District::where('state_id', $state)->orderBy('name')->get(['id', 'name', 'name_local']));
    }

    public function townships(int $district): JsonResponse
    {
        return response()->json(Township::where('district_id', $district)->orderBy('name')->get(['id', 'name', 'name_local']));
    }

    public function wards(int $township): JsonResponse
    {
        return response()->json(Ward::where('township_id', $township)->orderBy('name')->get(['id', 'name', 'name_local', 'postal_code']));
    }
}
