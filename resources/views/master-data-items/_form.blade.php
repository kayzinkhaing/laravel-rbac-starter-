@php
    $item = $data ?? null;
    $categories = \App\Models\MasterDataCategory::query()->orderBy('name')->get();
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="space-y-1.5 sm:col-span-2">
        <label for="master_data_category_id" class="field-label">
            Category
            <span class="ml-1 text-rose-600" aria-hidden="true">*</span>
            <span class="sr-only">(required)</span>
        </label>
        <select id="master_data_category_id" name="master_data_category_id" class="input-control">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) old('master_data_category_id', $item?->master_data_category_id) === (string) $category->id)>
                    {{ $category->name }} ({{ $category->key }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('master_data_category_id')" />
    </div>
    <x-crud.field name="key" label="Key" :value="$item?->key" placeholder="pending" maxlength="100" is-required />
    <x-crud.field name="value" label="Value" :value="$item?->value" placeholder="Pending" maxlength="255" is-required />
    <x-crud.field name="label" label="Display label" :value="$item?->label" maxlength="255" />
    <x-crud.field name="sort_order" label="Sort order" type="number" :value="$item?->sort_order ?? 0" min="0" />
    <div class="flex items-center pt-7">
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_active" value="1"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                @checked((bool) old('is_active', $item?->is_active ?? true))>
            Active item
        </label>
        <x-input-error :messages="$errors->get('is_active')" />
    </div>
    <x-crud.field name="description" label="Description" type="textarea"
        :value="$item?->description" maxlength="2000" rows="4" class="sm:col-span-2" />
</div>
