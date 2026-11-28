<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'terms' => ['accepted'],
            'recaptcha_token' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The Name field is required.',
            'email.required' => 'The Email field is required.',
            'password.required' => 'The Password field is required.',
            'password_confirmation.required' => 'The Confirm Password field is required.',
            'terms.accepted' => 'You must agree to the Terms and Conditions.',
        ];
    }
}
