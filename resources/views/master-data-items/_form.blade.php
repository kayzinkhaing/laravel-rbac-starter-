@php
    $item = $data ?? null;
    $categories = \App\Models\MasterDataCategory::query()
        ->orderBy('name')
        ->get();
@endphp

<div class="space-y-5">
    <div>
        <label for="master_data_category_id">Category</label>
        <select id="master_data_category_id" name="master_data_category_id"
                class="input-control w-full" required>
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) old('master_data_category_id', $item?->master_data_category_id) === (string) $category->id)>
                    {{ $category->name }} ({{ $category->key }})
                </option>
            @endforeach
        </select>
        @error('master_data_category_id') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="key">Key</label>
        <input id="key" name="key" type="text" class="input-control w-full"
               value="{{ old('key', $item?->key) }}" placeholder="pending"
               maxlength="100" required>
        @error('key') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="value">Value</label>
        <input id="value" name="value" type="text" class="input-control w-full"
               value="{{ old('value', $item?->value) }}" placeholder="Pending"
               maxlength="255" required>
        @error('value') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="label">Label</label>
        <input id="label" name="label" type="text" class="input-control w-full"
               value="{{ old('label', $item?->label) }}" maxlength="255">
        @error('label') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description" class="input-control w-full"
                  rows="4" maxlength="2000">{{ old('description', $item?->description) }}</textarea>
        @error('description') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sort_order">Sort Order</label>
        <input id="sort_order" name="sort_order" type="number" min="0"
               class="input-control w-full"
               value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
        @error('sort_order') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                   @checked((bool) old('is_active', $item?->is_active ?? true))>
            Active
        </label>
        @error('is_active') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
