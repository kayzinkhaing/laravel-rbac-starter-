<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mediable_type' => ['required', 'string'],
            'mediable_id' => ['required', 'integer'],
            'url' => ['required', 'string', 'max:255'],
            'original_filename' => ['nullable', 'string', 'max:255'],
            'mime_type' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'mediable_type.required' => 'A media owner type is required.',
            'mediable_id.required' => 'A media owner is required.',
            'url.required' => 'A media URL is required.',
        ];
    }
}
