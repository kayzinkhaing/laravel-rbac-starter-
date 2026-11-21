<?php

namespace App\Http\Requests;

use App\Support\AdminDashboardAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class permissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage permissions') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($this->route('permission')),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $permission = $this->route('permission');
            $permissionName = is_object($permission) ? $permission->name : '';
            $isReservedPermission = in_array(strtolower($permissionName), AdminDashboardAccess::PERMISSIONS, true);
            $isReservedName = in_array(strtolower(trim((string) $this->input('name'))), AdminDashboardAccess::PERMISSIONS, true);

            if (($isReservedPermission || $isReservedName) && ! AdminDashboardAccess::isSuperAdmin($this->user())) {
                $validator->errors()->add('name', 'Only a full administrator can create or change an administrative permission.');
            }
        });
    }
}
