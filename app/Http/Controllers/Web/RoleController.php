<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\rolesRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\roles;
use App\Support\AdminDashboardAccess;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected roles $roleService) {}

    public function index(Request $request): View|Response
    {
        $this->authorize('viewAny', Role::class);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['name', 'users_count', 'permissions_count'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $viewData = [
            'roles' => $this->roleService->paginateWithPermissions($filters),
            'filters' => $filters,
        ];

        return $request->ajax()
            ? response()->view('roles.partials.results', $viewData)
            : view('roles.index', $viewData);
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        return view('roles.create', [
            'permissions' => $this->availablePermissions(request()->user()),
        ]);
    }

    public function store(rolesRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);
        $this->roleService->createWithPermissions($request->validated());

        return redirect()->route('role.roles.index')->with('success', 'Role created.');
    }

    public function edit(string $roleSlug, Role $role): View
    {
        $this->authorize('update', $role);

        return view('roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->availablePermissions(request()->user()),
        ]);
    }

    public function update(rolesRequest $request, string $roleSlug, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);
        $this->roleService->updateWithPermissions($role, $request->validated());

        return redirect()->route('role.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(string $roleSlug, Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);
        $this->roleService->deleteRole($role);

        return redirect()->route('role.roles.index')->with('success', 'Role deleted.');
    }

    private function availablePermissions($user)
    {
        $query = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name');

        if (! AdminDashboardAccess::isSuperAdmin($user)) {
            $query->whereIn('id', $user->getAllPermissions()->modelKeys());
        }

        return $query->get();
    }
}
