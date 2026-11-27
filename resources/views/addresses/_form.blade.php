@csrf
<div class="surface-card grid gap-4 p-5 sm:grid-cols-2 sm:p-6" x-data="addressForm()">
    <div class="sm:col-span-2">
        <label class="field-label" for="address_line_1">Address line 1</label>
        <input class="input-control" id="address_line_1" name="address_line_1" required maxlength="255" value="{{ old('address_line_1', $address->address_line_1) }}">
        <x-input-error :messages="$errors->get('address_line_1')" />
    </div>
    <div class="sm:col-span-2">
        <label class="field-label" for="address_line_2">Address line 2</label>
        <input class="input-control" id="address_line_2" name="address_line_2" maxlength="255" value="{{ old('address_line_2', $address->address_line_2) }}">
    </div>
    <div>
        <label class="field-label" for="country_id">Country</label>
        <select class="input-control" id="country_id" name="country_id" x-ref="country" @change="load('states', $event.target.value, 'state_id')">
            <option value="">Select country</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}" @selected(old('country_id', $address->country_id) == $country->id)>{{ $country->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('country_id')" />
    </div>
    @foreach ([
        'state_id' => ['State / Region', 'states'],
        'district_id' => ['District', 'districts'],
        'township_id' => ['Township', 'townships'],
        'ward_id' => ['Ward / Village tract', 'wards'],
    ] as $field => [$label, $endpoint])
        <div>
            <label class="field-label" for="{{ $field }}">{{ $label }}</label>
            <select class="input-control" id="{{ $field }}" name="{{ $field }}" data-location-select="{{ $endpoint }}" @change="changed('{{ $field }}', $event.target.value)">
                <option value="">Select {{ strtolower($label) }}</option>
                @foreach ($locationOptions[$field] as $option)
                    <option value="{{ $option->id }}" @selected(old($field, $address->$field) == $option->id)>
                        {{ $option->name }}{{ $option->name_local ? ' ('.$option->name_local.')' : '' }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get($field)" />
        </div>
    @endforeach
    <div>
        <label class="field-label" for="building_name">Building name</label>
        <input class="input-control" id="building_name" name="building_name" value="{{ old('building_name', $address->building_name) }}">
    </div>
    <div>
        <label class="field-label" for="unit_number">Unit / Room</label>
        <input class="input-control" id="unit_number" name="unit_number" value="{{ old('unit_number', $address->unit_number) }}">
    </div>
    <div>
        <label class="field-label" for="postal_code">Postal code</label>
        <input class="input-control" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}">
    </div>
    <div>
        <label class="field-label" for="landmark">Landmark</label>
        <input class="input-control" id="landmark" name="landmark" value="{{ old('landmark', $address->landmark) }}">
    </div>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address->is_default))>
            Set as my default address
        </label>
    </div>
    <script>
        function addressForm() {
            const endpoints = {
                states: id => `/address-locations/countries/${id}/states`,
                districts: id => `/address-locations/states/${id}/districts`,
                townships: id => `/address-locations/districts/${id}/townships`,
                wards: id => `/address-locations/townships/${id}/wards`,
            };
            const next = {
                state_id: ['district_id', 'districts'],
                district_id: ['township_id', 'townships'],
                township_id: ['ward_id', 'wards'],
                ward_id: [],
            };
            return {
                async load(level, parent, target) {
                    const select = document.getElementById(target);
                    select.innerHTML = `<option value="">Loading...</option>`;
                    this.clearAfter(target);
                    if (!parent) {
                        select.innerHTML = `<option value="">Select ${target.replace('_id', '').replace('_', ' ')}</option>`;
                        return;
                    }
                    const response = await fetch(endpoints[level](parent), { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) {
                        select.innerHTML = `<option value="">Unable to load</option>`;
                        return;
                    }
                    const rows = await response.json();
                    select.innerHTML = `<option value="">Select ${target.replace('_id', '').replace('_', ' ')}</option>`;
                    rows.forEach(row => {
                        const option = document.createElement('option');
                        option.value = row.id;
                        option.textContent = row.name_local ? `${row.name} (${row.name_local})` : row.name;
                        select.appendChild(option);
                    });
                },
                changed(field, value) {
                    const config = next[field];
                    if (config.length) this.load(config[1], value, config[0]);
                },
                clearAfter(field) {
                    const chain = {
                        state_id: ['district_id', 'township_id', 'ward_id'],
                        district_id: ['township_id', 'ward_id'],
                        township_id: ['ward_id'],
                    };
                    (chain[field] || []).forEach(id => {
                        const element = document.getElementById(id);
                        if (element) element.innerHTML = `<option value="">Select ${id.replace('_id', '').replace('_', ' ')}</option>`;
                    });
                },
            };
        }
    </script>
</div>
