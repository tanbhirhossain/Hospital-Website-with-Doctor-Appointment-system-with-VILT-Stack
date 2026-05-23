<?php

namespace Modules\ACL\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Modules\ACL\Interfaces\UserRepositoryInterface;
use Spatie\Permission\Models\Role;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}

    public function getUsers(): LengthAwarePaginator
    {
        return $this->userRepo->all();
    }

    public function getUser(int $id): User
    {
        return $this->userRepo->findById($id);
    }

    /**
     * @return Collection<int, Role>
     */
    public function getAssignableRoles(): Collection
    {
        return Role::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array{name: string, email: string, password: string, roles?: array<int, int|string>}  $data
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->syncRoles($data['roles'] ?? []);

        return $user;
    }

    /**
     * @param  array{name: string, email: string, password?: string|null, roles?: array<int, int|string>}  $data
     */
    public function update(int $id, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user = $this->userRepo->update($updateData, $id);
        $user->syncRoles($data['roles'] ?? []);

        return $user;
    }

    public function destroy(int $id): bool
    {
        $user = $this->userRepo->findById($id);

        return $this->userRepo->delete($user);
    }
}
