@php
    $category = $data ?? null;
@endphp

<div class="space-y-5">
    <div>
        <label for="key">Key</label>
        <input id="key" name="key" type="text" class="input-control w-full"
               value="{{ old('key', $category?->key) }}"
               placeholder="order-status" maxlength="100" required>
        @error('key') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name">Name</label>
        <input id="name" name="name" type="text" class="input-control w-full"
               value="{{ old('name', $category?->name) }}"
               placeholder="Order Status" maxlength="150" required>
        @error('name') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description" class="input-control w-full"
                  rows="4" maxlength="2000">{{ old('description', $category?->description) }}</textarea>
        @error('description') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sort_order">Sort Order</label>
        <input id="sort_order" name="sort_order" type="number" min="0"
               class="input-control w-full"
               value="{{ old('sort_order', $category?->sort_order ?? 0) }}">
        @error('sort_order') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                   @checked((bool) old('is_active', $category?->is_active ?? true))>
            Active
        </label>
        @error('is_active') <p class="text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
