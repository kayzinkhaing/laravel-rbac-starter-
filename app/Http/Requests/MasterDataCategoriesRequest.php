<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MasterDataCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage master data') ?? false;
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');

        return [
            'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('master_data_categories', 'key')->ignore($categoryId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'The category key is required.',
            'key.unique' => 'This master data category key already exists.',
            'name.required' => 'The category name is required.',
        ];
    }
}
