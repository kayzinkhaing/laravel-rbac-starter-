<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MasterDataItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage master data') ?? false;
    }

    public function rules(): array
    {
        $itemId = $this->route('id');
        $categoryId = $this->input('master_data_category_id');

        return [
            'master_data_category_id' => [
                'required',
                'integer',
                'exists:master_data_categories,id',
            ],
            'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('master_data_items', 'key')
                    ->where(fn ($query) => $query->where('master_data_category_id', $categoryId))
                    ->ignore($itemId),
            ],
            'value' => ['required', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'master_data_category_id.required' => 'Please select a master data category.',
            'master_data_category_id.exists' => 'The selected master data category does not exist.',
            'key.unique' => 'This item key already exists in the selected category.',
            'value.required' => 'The item value is required.',
        ];
    }
}
