<?php

namespace App\Policies;

use App\Models\User;


class RolePolicy
{
public function viewAny(User $user): bool
{
    return $user->can('role.view');
}

public function create(User $user): bool
{
    return $user->can('role.create');
}

public function update(User $user): bool
{
    return $user->can('role.edit');
}

public function delete(User $user): bool
{
    return $user->can('role.delete');
}
}
