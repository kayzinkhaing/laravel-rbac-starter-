<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow all users to register
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'recaptcha_token' => ['nullable', 'string'],
            'image' => [
                'nullable',
                'file',           // ensures it's an uploaded file
                'image',          // must be an image
                'mimes:jpg,jpeg,png,gif',
                'max:2048',       // 2MB
            ],
        ];
    }
}
