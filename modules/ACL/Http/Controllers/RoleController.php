<?php

namespace Modules\ACL\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\ACL\Requests\StoreRoleRequest;
use Modules\ACL\Services\RoleService;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {
        // $this->authorizeResource(\Spatie\Permission\Models\Role::class, 'role');
    }

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('role.view'), 403);

        return Inertia::render('ACL::Index', [
            'roles' => $this->roleService->getAllRoles()->through(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->map(fn ($permission): array => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ])->values(),
                'users_count' => $role->users_count,
                'created_at' => $role->created_at?->toFormattedDateString(),
            ]),
            'permissions' => $this->roleService->getAllPermissions()
                ->map(fn ($permission): array => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'group' => str($permission->name)->before('.')->headline()->toString(),
                ])
                ->values(),
            'can' => [
                'create' => $request->user()?->can('role.create') === true,
                'edit' => $request->user()?->can('role.edit') === true,
                'delete' => $request->user()?->can('role.delete') === true,
            ],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->store($request->validated());

        return to_route('roles.index')->with('success', 'Role created successfully.');
    }

    public function update(StoreRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->update($role->id, $request->validated());

        return to_route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()?->can('role.delete'), 403);

        $this->roleService->destroy($role->id);

        return to_route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
