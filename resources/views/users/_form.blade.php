@csrf
@if (isset($user))
    @method('PUT')
@endif
<input type="hidden" name="roles_present" value="1">

<section class="surface-card p-5 sm:p-6">
    <h2 class="text-sm font-semibold text-slate-900">Account details</h2>
    <p class="mt-1 text-sm text-slate-500">Provide the user's name and sign-in email.</p>
    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        <div>
            <label for="first_name" class="field-label">First name</label>
            <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name ?? '') }}" class="input-control" required autocomplete="given-name">
            <x-input-error :messages="$errors->get('first_name')" />
        </div>
        <div>
            <label for="middle_name" class="field-label">Middle name <span class="font-normal text-slate-400">Optional</span></label>
            <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name', $user->middle_name ?? '') }}" class="input-control" autocomplete="additional-name">
            <x-input-error :messages="$errors->get('middle_name')" />
        </div>
        <div>
            <label for="last_name" class="field-label">Last name</label>
            <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name ?? '') }}" class="input-control" required autocomplete="family-name">
            <x-input-error :messages="$errors->get('last_name')" />
        </div>
        <div>
            <label for="email" class="field-label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" class="input-control" required autocomplete="email">
            <x-input-error :messages="$errors->get('email')" />
        </div>
        <div @class(['sm:col-span-2' => !isset($user)])>
            <label for="password" class="field-label">Password @if (isset($user))<span class="font-normal text-slate-400">Leave blank to keep current</span>@endif</label>
            <input id="password" name="password" type="password" class="input-control" @required(!isset($user)) autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" />
        </div>
        @if (!isset($user))
            <div>
                <label for="password_confirmation" class="field-label">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input-control" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
        @else
            <div>
                <label for="password_confirmation" class="field-label">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input-control" autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
        @endif
    </div>
</section>

<section class="surface-card p-5 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-900">Role assignment</h2>
            <p class="mt-1 text-sm text-slate-500">Permissions inherited by this user are based on their assigned roles.</p>
        </div>
        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $roles->count() }} available</span>
    </div>
    @if ($roles->isNotEmpty())
        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            @foreach ($roles as $role)
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-3 transition hover:border-indigo-200 hover:bg-indigo-50/30">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        @checked(in_array($role->id, old('roles', isset($user) ? $user->roles->modelKeys() : [])))>
                    <span>
                        <span class="block text-sm font-medium text-slate-700">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                        <span class="mt-0.5 block text-xs text-slate-500">{{ $role->permissions_count ?? $role->permissions->count() }} permissions</span>
                    </span>
                </label>
            @endforeach
        </div>
    @else
        <div class="mt-4 rounded-lg border border-dashed border-slate-200 px-4 py-6 text-center">
            <p class="text-sm text-slate-500">There are no roles to assign yet.</p>
            @can('create', \App\Models\Role::class)
                <a href="{{ route('role.roles.create') }}" class="mt-2 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-700">Create a role</a>
            @endcan
        </div>
    @endif
    <x-input-error :messages="$errors->get('roles')" />
</section>
