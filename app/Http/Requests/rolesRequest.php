<?php

namespace App\Http\Requests;

use App\Support\AdminDashboardAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class rolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage roles') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($this->route('role')),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [
                'integer',
                'distinct',
                Rule::exists('permissions', 'id')->where('guard_name', 'web'),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $requestedPermissionIds = collect($this->input('permissions', []))
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all();

            if (AdminDashboardAccess::isSuperAdmin($this->user())) {
                return;
            }

            if (array_diff($requestedPermissionIds, $this->user()->getAllPermissions()->modelKeys()) !== []) {
                $validator->errors()->add('permissions', 'You cannot grant permissions you do not have.');
            }
        });
    }
}
