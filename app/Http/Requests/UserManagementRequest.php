<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UserManagementRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->boolean('roles_present') && ! $this->exists('roles')) {
            $this->merge(['roles' => []]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage users') ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'roles_present' => ['sometimes', 'boolean'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => [
                'integer',
                'distinct',
                Rule::exists('roles', 'id')->where('guard_name', 'web'),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $roleIds = collect($this->input('roles', []))
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all();

            if ($roleIds === []) {
                return;
            }

            $allowedPermissionIds = $this->user()->getAllPermissions()->modelKeys();
            $hasUnmanageablePermissions = Role::query()
                ->with('permissions')
                ->whereIn('id', $roleIds)
                ->get()
                ->contains(fn (Role $role) => array_diff($role->permissions->modelKeys(), $allowedPermissionIds) !== []);

            if ($hasUnmanageablePermissions) {
                $validator->errors()->add('roles', 'You cannot assign a role that grants permissions you do not have.');
            }
        });
    }
}
