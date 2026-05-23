<?php

namespace Modules\ACL\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\ACL\Interfaces\RoleRepositoryInterface;
use Override;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    #[Override]
    public function create(array $data): Role
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    #[Override]
    public function update(int $id, array $data): Role
    {
        $role = $this->findById($id);
        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? $role->guard_name,
        ]);

        return $role;
    }

    #[Override]
    public function delete(Role $role): bool
    {
        return (bool) $role->delete();
    }

    #[Override]
    public function all(): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions:id,name')
            ->withCount('users')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    #[Override]
    public function findById(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }
}
