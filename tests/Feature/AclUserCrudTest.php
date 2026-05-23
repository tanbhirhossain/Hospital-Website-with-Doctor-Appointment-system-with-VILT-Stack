<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function aclUserWithUserPermissions(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user->givePermissionTo($permissions);

    return $user;
}

test('guests are redirected from users management', function () {
    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('users need user view permission to manage users', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('users.index'))
        ->assertForbidden();
});

test('acl users page renders user and role data', function () {
    $admin = aclUserWithUserPermissions(['user.view', 'user.create', 'user.edit', 'user.delete']);
    $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
    $user = User::factory()->create(['name' => 'Managed User']);
    $user->assignRole($role);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ACL::Users/Index', false)
            ->has('users.data')
            ->where('users.data.0.name', 'Managed User')
            ->where('users.data.0.roles.0.name', 'Manager')
            ->where('roles.0.name', 'Manager')
            ->where('can.create', true)
            ->where('can.edit', true)
            ->where('can.delete', true)
        );
});

test('authorized users can create users with roles', function () {
    $admin = aclUserWithUserPermissions(['user.view', 'user.create']);
    $role = Role::create(['name' => 'Support', 'guard_name' => 'web']);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Support User',
            'email' => 'support@example.com',
            'password' => 'password',
            'roles' => [$role->id],
        ])
        ->assertRedirect(route('users.index'));

    $user = User::query()->where('email', 'support@example.com')->firstOrFail();

    expect(Hash::check('password', $user->password))->toBeTrue()
        ->and($user->hasRole($role))->toBeTrue();
});

test('authorized users can update users and sync roles', function () {
    $admin = aclUserWithUserPermissions(['user.view', 'user.edit']);
    $oldRole = Role::create(['name' => 'Old Role', 'guard_name' => 'web']);
    $newRole = Role::create(['name' => 'New Role', 'guard_name' => 'web']);
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'password' => Hash::make('old-password'),
    ]);
    $user->assignRole($oldRole);

    $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => null,
            'roles' => [$newRole->id],
        ])
        ->assertRedirect(route('users.index'));

    $user->refresh();

    expect($user->name)->toBe('Updated User')
        ->and($user->email)->toBe('updated@example.com')
        ->and(Hash::check('old-password', $user->password))->toBeTrue()
        ->and($user->hasRole($newRole))->toBeTrue()
        ->and($user->hasRole($oldRole))->toBeFalse();
});

test('user emails must be unique', function () {
    $admin = aclUserWithUserPermissions(['user.view', 'user.create']);

    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin)
        ->from(route('users.index'))
        ->post(route('users.store'), [
            'name' => 'Duplicate User',
            'email' => 'taken@example.com',
            'password' => 'password',
            'roles' => [],
        ])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('email');
});

test('authorized users can delete users', function () {
    $admin = aclUserWithUserPermissions(['user.view', 'user.delete']);
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));

    expect(User::find($user->id))->toBeNull();
});

test('users cannot delete their own account from user management', function () {
    $admin = aclUserWithUserPermissions(['user.view', 'user.delete']);

    $this->actingAs($admin)
        ->from(route('users.index'))
        ->delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('user');

    expect(User::find($admin->id))->not->toBeNull();
});
