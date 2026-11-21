<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\UserManagementRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\users;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private users $userService) {}

    public function index(Request $request): View|Response
    {
        $this->authorize('viewAny', User::class);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'web')],
            'verified' => ['nullable', 'in:verified,unverified'],
            'sort' => ['nullable', Rule::in(['name', 'email', 'email_verified_at', 'created_at', 'roles_count'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $viewData = [
            'users' => $this->userService->paginateForAdministration($filters),
            'roles' => $request->ajax()
                ? collect()
                : Role::query()->where('guard_name', 'web')->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ];

        return $request->ajax()
            ? response()->view('users.partials.results', $viewData)
            : view('users.index', $viewData);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'roles' => $this->assignableRoles(),
        ]);
    }

    public function store(UserManagementRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);
        $this->userService->createForAdministration($request->validated());

        return redirect()->route('role.users.index')->with('success', 'User created.');
    }

    public function show(string $roleSlug, User $user): View
    {
        $this->authorize('view', $user);

        return view('users.show', [
            'user' => $this->userService->loadForAdministration($user),
        ]);
    }

    public function edit(string $roleSlug, User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $this->userService->loadForAdministration($user),
            'roles' => $this->assignableRoles(),
        ]);
    }

    public function update(UserManagementRequest $request, string $roleSlug, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $this->userService->updateForAdministration($user, $request->validated());

        return redirect()->route('role.users.show', ['user' => $user])->with('success', 'User updated.');
    }

    public function destroy(string $roleSlug, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);
        $this->userService->deleteForAdministration($user);

        return redirect()->route('role.users.index')->with('success', 'User deleted.');
    }

    private function assignableRoles(): Collection
    {
        $permissionIds = request()->user()->getAllPermissions()->modelKeys();

        return Role::query()
            ->where('guard_name', 'web')
            ->whereDoesntHave('permissions', fn ($query) => $query->whereNotIn('permissions.id', $permissionIds))
            ->withCount('permissions')
            ->orderBy('name')
            ->get();
    }
}
