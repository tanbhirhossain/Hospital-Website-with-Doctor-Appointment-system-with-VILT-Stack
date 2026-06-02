<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\DOCTOR\Models\Department;

test('guests are redirected from departments management', function () {
    $this->get(route('departments.index'))->assertRedirect(route('login'));
});

test('departments page renders department data', function () {
    $user = User::factory()->create();
    $parent = Department::factory()->create([
        'created_by' => $user->id,
        'name' => 'Medicine',
        'slug' => 'medicine',
    ]);
    Department::factory()->create([
        'created_by' => $user->id,
        'parent_id' => $parent->id,
        'name' => 'Cardiology',
        'slug' => 'cardiology',
        'shortDesc' => 'Heart care',
        'text_icon' => 'heart-pulse',
    ]);

    $this->actingAs($user)
        ->get(route('departments.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DOCTOR::Department/Index', false)
            ->has('departments.data', 2)
            ->where('departments.data.0.name', 'Cardiology')
            ->where('departments.data.0.parent.name', 'Medicine')
            ->where('departments.data.0.text_icon', 'heart-pulse')
            ->where('parentOptions.0.name', 'Cardiology')
            ->where('filters.search', '')
            ->where('filters.parent_id', null)
            ->where('filters.media', '')
            ->where('can.create', true)
            ->where('can.edit', true)
            ->where('can.delete', true)
        );
});

test('departments page can be searched and filtered', function () {
    $user = User::factory()->create();
    $parent = Department::factory()->create([
        'created_by' => $user->id,
        'name' => 'Medicine',
        'slug' => 'medicine',
    ]);
    Department::factory()->create([
        'created_by' => $user->id,
        'parent_id' => $parent->id,
        'name' => 'Cardiology',
        'slug' => 'cardiology',
        'shortDesc' => 'Heart care',
    ])->addMedia(UploadedFile::fake()->image('cardiology.jpg'))->toMediaCollection('image');
    Department::factory()->create([
        'created_by' => $user->id,
        'name' => 'Neurology',
        'slug' => 'neurology',
        'shortDesc' => 'Brain care',
    ]);

    $this->actingAs($user)
        ->get(route('departments.index', [
            'search' => 'heart',
            'parent_id' => $parent->id,
            'media' => 'with_media',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DOCTOR::Department/Index', false)
            ->has('departments.data', 1)
            ->where('departments.data.0.name', 'Cardiology')
            ->where('filters.search', 'heart')
            ->where('filters.parent_id', $parent->id)
            ->where('filters.media', 'with_media')
        );
});

test('authenticated users can create departments', function () {
    $user = User::factory()->create();
    $parent = Department::factory()->create([
        'created_by' => $user->id,
        'name' => 'Medicine',
        'slug' => 'medicine',
    ]);

    $this->actingAs($user)
        ->post(route('departments.store'), [
            'name' => 'Cardiology',
            'slug' => 'cardiology',
            'shortDesc' => 'Heart care',
            'descriptions' => 'Cardiology department details.',
            'text_icon' => 'heart-pulse',
            'parent_id' => $parent->id,
            'banner_image' => UploadedFile::fake()->image('banner.jpg', 1600, 500),
            'image' => UploadedFile::fake()->image('department.jpg', 900, 600),
            'icon_image' => UploadedFile::fake()->image('icon.jpg', 128, 128),
        ])
        ->assertRedirect(route('departments.index'));

    $department = Department::query()->where('slug', 'cardiology')->firstOrFail();

    expect($department->name)->toBe('Cardiology')
        ->and($department->created_by)->toBe($user->id)
        ->and($department->parent_id)->toBe($parent->id)
        ->and($department->getFirstMedia('banner_image'))->not->toBeNull()
        ->and($department->getFirstMedia('image'))->not->toBeNull()
        ->and($department->getFirstMedia('icon_image'))->not->toBeNull();
});

test('authenticated users can update departments', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create([
        'created_by' => $user->id,
        'name' => 'Cardiology',
        'slug' => 'cardiology',
    ]);

    $this->actingAs($user)
        ->put(route('departments.update', $department), [
            'name' => 'Clinical Cardiology',
            'slug' => 'clinical-cardiology',
            'shortDesc' => 'Updated heart care',
            'descriptions' => 'Updated department details.',
            'text_icon' => 'activity',
            'parent_id' => null,
            'image' => UploadedFile::fake()->image('updated-department.jpg', 900, 600),
        ])
        ->assertRedirect(route('departments.index'));

    $department->refresh();

    expect($department->name)->toBe('Clinical Cardiology')
        ->and($department->slug)->toBe('clinical-cardiology')
        ->and($department->shortDesc)->toBe('Updated heart care')
        ->and($department->text_icon)->toBe('activity')
        ->and($department->getFirstMedia('image'))->not->toBeNull();
});

test('department slugs must be unique', function () {
    $user = User::factory()->create();

    Department::factory()->create([
        'created_by' => $user->id,
        'slug' => 'cardiology',
    ]);

    $this->actingAs($user)
        ->from(route('departments.index'))
        ->post(route('departments.store'), [
            'name' => 'Cardiology',
            'slug' => 'cardiology',
        ])
        ->assertRedirect(route('departments.index'))
        ->assertSessionHasErrors('slug');
});

test('authenticated users can delete departments', function () {
    $user = User::factory()->create();
    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('departments.destroy', $department))
        ->assertRedirect(route('departments.index'));

    expect(Department::find($department->id))->toBeNull()
        ->and(Department::withTrashed()->find($department->id))->not->toBeNull();
});
