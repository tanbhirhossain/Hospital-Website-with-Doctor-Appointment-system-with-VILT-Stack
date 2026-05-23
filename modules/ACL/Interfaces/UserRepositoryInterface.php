<?php

namespace Modules\ACL\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function all(): LengthAwarePaginator;

    public function findById(int $id): User;

    /**
     * @param  array{name: string, email: string, password: string}  $data
     */
    public function create(array $data): User;

    /**
     * @param  array{name: string, email: string, password?: string}  $data
     */
    public function update(array $data, int $id): User;

    public function delete(User $user): bool;
}
