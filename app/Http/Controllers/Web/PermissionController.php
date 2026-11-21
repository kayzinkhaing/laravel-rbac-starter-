<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\permissionsRequest;
use App\Models\Permission;
use App\Services\Permissions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected Permissions $permissionService) {}

    public function index(Request $request): View|Response
    {
        $this->authorize('viewAny', Permission::class);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['name', 'roles_count'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $viewData = [
            'permissions' => $this->permissionService->paginate(
                $filters['search'] ?? null,
                $filters['sort'] ?? 'name',
                $filters['direction'] ?? 'asc'
            ),
            'search' => $filters['search'] ?? '',
            'filters' => $filters,
        ];

        return $request->ajax()
            ? response()->view('permissions.partials.results', $viewData)
            : view('permissions.index', $viewData);
    }

    public function create(): View
    {
        $this->authorize('create', Permission::class);

        return view('permissions.create');
    }

    public function store(permissionsRequest $request): RedirectResponse
    {
        $this->authorize('create', Permission::class);
        $this->permissionService->createPermission($request->validated());

        return redirect()->route('role.permissions.index')->with('success', 'Permission created.');
    }

    public function edit(string $roleSlug, Permission $permission): View
    {
        $this->authorize('update', $permission);

        return view('permissions.edit', compact('permission'));
    }

    public function update(permissionsRequest $request, string $roleSlug, Permission $permission): RedirectResponse
    {
        $this->authorize('update', $permission);
        $this->permissionService->updatePermission($permission, $request->validated());

        return redirect()->route('role.permissions.index')->with('success', 'Permission updated.');
    }

    public function destroy(string $roleSlug, Permission $permission): RedirectResponse
    {
        $this->authorize('delete', $permission);
        $this->permissionService->deletePermission($permission);

        return redirect()->route('role.permissions.index')->with('success', 'Permission deleted.');
    }
}
