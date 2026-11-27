@extends(($roleDashboard ?? false) ? 'layouts.app' : 'layouts.public')

@section('title', 'My profile')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-7">
        <p class="text-sm font-medium text-indigo-600">Account settings</p>
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">My profile</h1>
        <p class="mt-2 text-sm text-slate-500">View and update your personal information and security settings.</p>
    </div>

    <div class="mb-6 surface-card p-5 sm:p-6">
        <div class="flex flex-wrap items-center gap-4" x-data="{ preview: null }">
            @if ($user->profileImage())
                <img src="{{ route('profile.image', ['user' => $user]) }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover ring-4 ring-slate-100">
            @else
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-xl font-semibold text-indigo-700">
                    {{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0 flex-1">
                <h2 class="truncate text-base font-semibold text-slate-900">{{ $user->name }}</h2>
                <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
                <form method="POST" action="{{ ($roleDashboard ?? false) ? route('role.profile.image.update', ['roleSlug' => $roleSlugs[$activeRole->id]]) : route('profile.image.update') }}" enctype="multipart/form-data" class="mt-3 flex flex-wrap items-center gap-2">
                    @csrf
                    <label class="btn-secondary cursor-pointer px-3">
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="preview = URL.createObjectURL($event.target.files[0]); $el.closest('form').submit()">
                        Change photo
                    </label>
                    @if ($user->profileImage())
                        <button type="submit" form="delete-profile-image" class="text-sm font-medium text-rose-600 hover:text-rose-700">Remove</button>
                    @endif
                </form>
                <form id="delete-profile-image" method="POST" action="{{ ($roleDashboard ?? false) ? route('role.profile.image.delete', ['roleSlug' => $roleSlugs[$activeRole->id]]) : route('profile.image.delete') }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>
            <span @class([
                'rounded-full px-2.5 py-1 text-xs font-medium',
                'bg-emerald-50 text-emerald-700' => $user->hasVerifiedEmail(),
                'bg-amber-50 text-amber-700' => !$user->hasVerifiedEmail(),
            ])>
                {{ $user->hasVerifiedEmail() ? 'Verified email' : 'Email not verified' }}
            </span>
        </div>
        @if ($user->roles->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                @foreach ($user->roles as $role)
                    <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                @endforeach
            </div>
        @endif
    </div>


    {{-- Saved addresses --}}
    <section class="surface-card mb-6 p-5 sm:p-6"
             x-data="{ addressFormOpen: {{ (old('_address_form') || request()->filled('edit_address')) ? 'true' : 'false' }} }">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-slate-900">My addresses</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Your saved delivery and contact addresses.
                </p>
            </div>

            <button type="button"
                    class="btn-primary"
                    @click="addressFormOpen = !addressFormOpen">
                <span x-text="addressFormOpen ? 'Close form' : 'Add address'"></span>
            </button>
        </div>

        <div class="mt-5 space-y-3">
            @forelse ($addresses as $savedAddress)
                <div class="rounded-lg border border-slate-200 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-medium text-slate-900">
                            {{ $savedAddress->building_name ?: 'Saved address' }}
                        </p>

                        <div class="flex items-center gap-2">
                            @if ($savedAddress->is_default)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    Default
                                </span>
                            @endif

                            <a href="{{ request()->fullUrlWithQuery([
                                'edit_address' => $savedAddress->getKey()
                            ]) }}#address-form"
                               class="btn-secondary">
                                Edit
                            </a>
                        </div>
                    </div>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        {{ collect([
                            $savedAddress->address_line_1,
                            $savedAddress->address_line_2,
                            $savedAddress->unit_number,
                            $savedAddress->ward?->name,
                            $savedAddress->township?->name,
                            $savedAddress->district?->name,
                            $savedAddress->state?->name,
                            $savedAddress->country?->name,
                            $savedAddress->postal_code,
                        ])->filter()->implode(', ') }}
                    </p>

                    @if ($savedAddress->landmark)
                        <p class="mt-1 text-xs text-slate-500">
                            Landmark: {{ $savedAddress->landmark }}
                        </p>
                    @endif
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 px-4 py-6 text-center">
                    <p class="text-sm font-medium text-slate-700">
                        No saved addresses yet.
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        Click Add address to create your first address.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Inline address creation / editing form --}}
        <div id="address-form" x-cloak x-show="addressFormOpen" x-transition class="mt-5">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-slate-900">
                    {{ $address->exists ? 'Edit your address' : 'Create your address' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    Enter your address and select your Myanmar administrative locations.
                </p>
            </div>

            <form method="POST"
                  action="{{ $address->exists
                      ? route('addresses.update', $address)
                      : route('addresses.store') }}">
                @csrf

                @if ($address->exists)
                    @method('PUT')
                @endif

                <input type="hidden" name="_address_form" value="1">

                @include('addresses._form', [
                    'address' => $address,
                    'countries' => $countries,
                    'locationOptions' => $locationOptions,
                ])

                <div class="mt-4 flex justify-end gap-3">
                    <a href="{{ request()->url() }}" class="btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn-primary">
                        {{ $address->exists ? 'Update address' : 'Save address' }}
                    </button>
                </div>
            </form>
        </div>

    </section>

    <div class="space-y-5">
        <section class="surface-card p-5 sm:p-6">
            @include('profile.partials.update-profile-information-form', ['user' => $user])
        </section>
        <section class="surface-card p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Two-factor authentication</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        {{ $user->two_factor_enabled ? 'Two-factor authentication is active on this account.' : 'Add an authenticator app for an extra sign-in step.' }}
                    </p>
                </div>
                <a href="{{ route(($roleDashboard ?? false) ? 'role.twofactor.setup' : 'twofactor.setup') }}" class="btn-secondary">
                    {{ $user->two_factor_enabled ? 'Manage 2FA' : 'Set up 2FA' }}
                </a>
            </div>
        </section>
        <section class="surface-card p-5 sm:p-6">
            @include('profile.partials.update-password-form')
        </section>
        <section class="surface-card border-rose-200 p-5 sm:p-6">
            @include('profile.partials.delete-user-form')
        </section>
    </div>
</section>
@endsection
