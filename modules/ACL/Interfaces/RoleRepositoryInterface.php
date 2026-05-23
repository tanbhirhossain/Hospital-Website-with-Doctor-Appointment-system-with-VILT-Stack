<?php

namespace Modules\ACL\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface
{
    public function all(): LengthAwarePaginator;

    public function findById(int $id): Role;

    /**
     * @param  array{name: string, guard_name?: string}  $data
     */
    public function create(array $data): Role;

    /**
     * @param  array{name: string, guard_name?: string}  $data
     */
    public function update(int $id, array $data): Role;

    public function delete(Role $role): bool;
}
