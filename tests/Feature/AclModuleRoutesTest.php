<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function userWithAclPermissions(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user->givePermissionTo($permissions);

    return $user;
}

test('guests are redirected from roles management', function () {
    $this->get(route('roles.index'))->assertRedirect(route('login'));
});

test('users need role view permission to manage roles', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('roles.index'))
        ->assertForbidden();
});

test('acl roles page renders role and permission data', function () {
    $user = userWithAclPermissions(['role.view', 'role.create', 'role.edit', 'role.delete']);
    $permission = Permission::findOrCreate('product.view', 'web');
    $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $this->actingAs($user)
        ->get(route('roles.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ACL::Index', false)
            ->has('roles.data', 1)
            ->where('roles.data.0.name', 'Manager')
            ->where('roles.data.0.permissions.0.name', 'product.view')
            ->where('permissions.0.name', 'product.view')
            ->where('can.create', true)
            ->where('can.edit', true)
            ->where('can.delete', true)
        );
});

test('authorized users can create roles with permissions', function () {
    $user = userWithAclPermissions(['role.view', 'role.create']);
    $permission = Permission::findOrCreate('product.create', 'web');

    $this->actingAs($user)
        ->post(route('roles.store'), [
            'name' => 'Inventory Manager',
            'permissions' => [$permission->id],
        ])
        ->assertRedirect(route('roles.index'));

    $role = Role::findByName('Inventory Manager', 'web');

    expect($role->hasPermissionTo($permission))->toBeTrue();
});

test('authorized users can update roles and sync permissions', function () {
    $user = userWithAclPermissions(['role.view', 'role.edit']);
    $oldPermission = Permission::findOrCreate('product.view', 'web');
    $newPermission = Permission::findOrCreate('product.edit', 'web');
    $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
    $role->givePermissionTo($oldPermission);

    $this->actingAs($user)
        ->put(route('roles.update', $role), [
            'name' => 'Senior Editor',
            'permissions' => [$newPermission->id],
        ])
        ->assertRedirect(route('roles.index'));

    $role->refresh();

    expect($role->name)->toBe('Senior Editor')
        ->and($role->hasPermissionTo($newPermission))->toBeTrue()
        ->and($role->hasPermissionTo($oldPermission))->toBeFalse();
});

test('role names must be unique per web guard', function () {
    $user = userWithAclPermissions(['role.view', 'role.create']);

    Role::create(['name' => 'Manager', 'guard_name' => 'web']);

    $this->actingAs($user)
        ->from(route('roles.index'))
        ->post(route('roles.store'), [
            'name' => 'Manager',
            'permissions' => [],
        ])
        ->assertRedirect(route('roles.index'))
        ->assertSessionHasErrors('name');
});

test('authorized users can delete roles', function () {
    $user = userWithAclPermissions(['role.view', 'role.delete']);
    $role = Role::create(['name' => 'Temporary', 'guard_name' => 'web']);

    $this->actingAs($user)
        ->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'));

    expect(Role::find($role->id))->toBeNull();
});

test('acl seeder assigns tanbhir as super admin', function () {
    User::factory()->create([
        'email' => 'amz.tanbhir@gmail.com',
    ]);

    $this->seed(\Database\Seeders\AclPermissionSeeder::class);

    $user = User::query()->where('email', 'amz.tanbhir@gmail.com')->firstOrFail();

    expect($user->hasRole('Super Admin'))->toBeTrue()
        ->and($user->can('role.view'))->toBeTrue()
        ->and($user->can('role.create'))->toBeTrue()
        ->and($user->can('role.edit'))->toBeTrue()
        ->and($user->can('role.delete'))->toBeTrue();
});
