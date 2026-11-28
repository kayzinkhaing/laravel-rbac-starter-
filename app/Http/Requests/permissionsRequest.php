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
            $oldNameIsSystemCapability = is_object($permission)
                && $permission->name === 'manage system permissions';
            $newNameIsSystemCapability = trim((string) $this->input('name'))
                === 'manage system permissions';

            if (($oldNameIsSystemCapability || $newNameIsSystemCapability)
                && ! AdminDashboardAccess::isSuperAdmin($this->user())) {
                $validator->errors()->add(
                    'name',
                    'Only a system administrator can create, rename, or modify the system permission.'
                );
            }
        });
    }
}
