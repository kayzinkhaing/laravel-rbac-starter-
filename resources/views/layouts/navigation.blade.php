<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden"
    aria-hidden="true"
></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-slate-800 bg-slate-950 text-slate-300 transition-[width,transform] duration-200 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'lg:w-[5.5rem]': sidebarCollapsed }"
    aria-label="Main navigation"
>
    <div class="flex h-[4.5rem] items-center gap-3 border-b border-slate-800 px-5">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500 text-white">
            <x-icon name="layers" class="h-[18px] w-[18px]" />
        </span>
        <span class="truncate text-sm font-semibold tracking-wide text-white" :class="{ 'lg:hidden': sidebarCollapsed }">
            {{ config('app.name', 'Workspace') }}
        </span>
    </div>

    <div class="flex-1 overflow-y-auto px-3 py-5">
        @if (isset($availableRoles) && $availableRoles->count() > 1)
            <label for="active-role-switcher" class="sidebar-label">Active role</label>
            <form method="POST" action="{{ route('active-role.update') }}" class="mb-5">
                @csrf
                <select id="active-role-switcher" name="role_id" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-200 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/40"
                    aria-label="Switch active role" onchange="this.form.submit()">
                    @foreach ($availableRoles as $role)
                        <option value="{{ $role->id }}" @selected(($activeRole ?? null)?->id === $role->id)>
                            {{ \Illuminate\Support\Str::headline($role->name) }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
        <p class="sidebar-label" :class="{ 'lg:hidden': sidebarCollapsed }">Workspace</p>
        <nav class="space-y-1" aria-label="Workspace">
            @if (($activeRole ?? null) || auth()->user()->can('access-admin-dashboard'))
                <a href="{{ ($activeRole ?? null) ? route('role.dashboard', ['roleSlug' => $roleSlugs[$activeRole->id]]) : route('dashboard') }}" @click="sidebarOpen = false" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('dashboard', 'role.dashboard')]) :title="sidebarCollapsed ? 'Dashboard' : null">
                    <x-icon name="layout-dashboard" class="h-[18px] w-[18px] shrink-0" />
                    <span :class="{ 'lg:hidden': sidebarCollapsed }">Dashboard</span>
                </a>
            @endif
            @can('viewAny', \App\Models\User::class)
                <a href="{{ route('role.users.index') }}" @click="sidebarOpen = false" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('role.users.*')]) :title="sidebarCollapsed ? 'Users' : null">
                    <x-icon name="users" class="h-[18px] w-[18px] shrink-0" />
                    <span :class="{ 'lg:hidden': sidebarCollapsed }">Users</span>
                </a>
            @endcan
            @can('viewAny', \App\Models\Role::class)
                <a href="{{ route('role.roles.index') }}" @click="sidebarOpen = false" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('role.roles.*')]) :title="sidebarCollapsed ? 'Roles' : null">
                    <x-icon name="shield" class="h-[18px] w-[18px] shrink-0" />
                    <span :class="{ 'lg:hidden': sidebarCollapsed }">Roles</span>
                </a>
            @endcan
            @can('viewAny', \App\Models\Permission::class)
                <a href="{{ route('role.permissions.index') }}" @click="sidebarOpen = false" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('role.permissions.*')]) :title="sidebarCollapsed ? 'Permissions' : null">
                    <x-icon name="key-round" class="h-[18px] w-[18px] shrink-0" />
                    <span :class="{ 'lg:hidden': sidebarCollapsed }">Permissions</span>
                </a>
            @endcan
        </nav>

        <p class="sidebar-label mt-8" :class="{ 'lg:hidden': sidebarCollapsed }">Account</p>
        <nav class="space-y-1" aria-label="Account">
            <a href="{{ \App\Support\AdminDashboardAccess::profileUrlFor(auth()->user()) }}" @click="sidebarOpen = false" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('profile.*', 'role.profile*')]) :title="sidebarCollapsed ? 'My profile' : null">
                <x-icon name="user-round" class="h-[18px] w-[18px] shrink-0" />
                <span :class="{ 'lg:hidden': sidebarCollapsed }">My profile</span>
            </a>
        </nav>
    </div>

    <div class="border-t border-slate-800 p-3">
        <div class="flex items-center gap-3 rounded-lg px-2 py-2">
            @if (auth()->user()->profileImage())
                <img src="{{ route('profile.image', ['user' => auth()->user()]) }}" alt="" class="h-8 w-8 shrink-0 rounded-full object-cover ring-2 ring-slate-800">
            @else
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-800 text-xs font-semibold text-slate-200">
                    {{ strtoupper(substr(auth()->user()->name ?: auth()->user()->email, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0 flex-1" :class="{ 'lg:hidden': sidebarCollapsed }">
                <p class="truncate text-sm font-medium text-slate-100">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
