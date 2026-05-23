<?php

namespace Modules\ACL\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\ACL\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository
    ) {}

    public function getAllRoles(): LengthAwarePaginator
    {
        return $this->roleRepository->all();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getAllPermissions(): Collection
    {
        return Permission::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{name: string, permissions?: array<int, int|string>}  $data
     */
    public function store(array $data): Role
    {
        $role = $this->roleRepository->create($data);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }

    /**
     * @param  array{name: string, permissions?: array<int, int|string>}  $data
     */
    public function update(int $id, array $data): Role
    {
        $role = $this->roleRepository->update($id, $data);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }

    public function destroy(int $id): bool
    {
        $role = $this->roleRepository->findById($id);

        return $this->roleRepository->delete($role);
    }
}
