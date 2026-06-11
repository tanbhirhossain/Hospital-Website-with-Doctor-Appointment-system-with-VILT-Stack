<?php

use App\Models\User;

it('redirects guests to the login page when visiting admin appointments', function () {
    $response = $this->get('/admin/appointments');

    $response->assertRedirect('/login');
});

test('verified users can access the admin appointments page', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    $response = $this->get('/admin/appointments');

    $response->assertStatus(200);
});
