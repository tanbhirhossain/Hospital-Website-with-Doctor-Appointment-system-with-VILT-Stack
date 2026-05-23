<?php

namespace Modules\ACL\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\ACL\Interfaces\UserRepositoryInterface;
use Override;

class UserRepository implements UserRepositoryInterface
{
    #[Override]
    public function all(): LengthAwarePaginator
    {
        return User::query()
            ->with('roles:id,name')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    #[Override]
    public function findById(int $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    #[Override]
    public function create(array $data): User
    {
        return User::create($data);
    }

    #[Override]
    public function update(array $data, int $id): User
    {
        $user = $this->findById($id);
        $user->update($data);

        return $user;
    }

    #[Override]
    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
