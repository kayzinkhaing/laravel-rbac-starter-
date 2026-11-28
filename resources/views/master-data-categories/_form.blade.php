@php($category = $data ?? null)

<div class="grid gap-5 sm:grid-cols-2">
    <x-crud.field name="key" label="Key" :value="$category?->key"
        placeholder="order-status" maxlength="100" is-required />
    <x-crud.field name="name" label="Name" :value="$category?->name"
        placeholder="Order Status" maxlength="150" is-required />
    <x-crud.field name="sort_order" label="Sort order" type="number"
        :value="$category?->sort_order ?? 0" min="0" />
    <div class="flex items-center pt-7">
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input type="checkbox" name="is_active" value="1"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                @checked((bool) old('is_active', $category?->is_active ?? true))>
            Active category
        </label>
        <x-input-error :messages="$errors->get('is_active')" />
    </div>
    <x-crud.field name="description" label="Description" type="textarea"
        :value="$category?->description" maxlength="2000" rows="4" class="sm:col-span-2" />
</div>
