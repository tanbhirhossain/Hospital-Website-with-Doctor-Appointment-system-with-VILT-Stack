<?php

namespace Modules\ACL\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\ACL\Requests\StoreUserRequest;
use Modules\ACL\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('user.view'), 403);

        return Inertia::render('ACL::Users/Index', [
            'users' => $this->userService->getUsers()->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->map(fn ($role): array => [
                    'id' => $role->id,
                    'name' => $role->name,
                ])->values(),
                'created_at' => $user->created_at?->toFormattedDateString(),
            ]),
            'roles' => $this->userService->getAssignableRoles()
                ->map(fn ($role): array => [
                    'id' => $role->id,
                    'name' => $role->name,
                ])
                ->values(),
            'can' => [
                'create' => $request->user()?->can('user.create') === true,
                'edit' => $request->user()?->can('user.edit') === true,
                'delete' => $request->user()?->can('user.delete') === true,
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return to_route('users.index')->with('success', 'User created successfully.');
    }

    public function update(StoreUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user->id, $request->validated());

        return to_route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->can('user.delete'), 403);

        if ($request->user()?->is($user)) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        $this->userService->destroy($user->id);

        return to_route('users.index')->with('success', 'User deleted successfully.');
    }
}
