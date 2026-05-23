<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

test('user creation is recorded in the activity log', function () {
    $user = User::factory()->create([
        'name' => 'Activity User',
        'email' => 'activity@example.com',
    ]);

    $activity = Activity::query()
        ->where('log_name', 'user')
        ->where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('event', 'created')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->description)->toBe('User created')
        ->and($activity->properties->get('attributes'))->toMatchArray([
            'name' => 'Activity User',
            'email' => 'activity@example.com',
        ])
        ->and($activity->properties->get('attributes'))->not->toHaveKey('password');
});

test('user updates record only dirty public profile fields', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    Activity::query()->delete();

    $user->update([
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $activity = Activity::query()
        ->where('log_name', 'user')
        ->where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('event', 'updated')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->description)->toBe('User updated')
        ->and($activity->properties->get('attributes'))->toMatchArray([
            'name' => 'New Name',
            'email' => 'new@example.com',
        ])
        ->and($activity->properties->get('old'))->toMatchArray([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
});

test('password only changes are not recorded in the user activity log', function () {
    $user = User::factory()->create();

    Activity::query()->delete();

    $user->update([
        'password' => Hash::make('new-password'),
    ]);

    expect(Activity::query()->where('log_name', 'user')->count())->toBe(0);
});
