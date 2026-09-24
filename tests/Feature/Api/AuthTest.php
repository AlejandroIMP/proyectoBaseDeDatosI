<?php

use App\Models\User;

test('login returns a token and the user', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret-pass',
        'device_name' => 'tests',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'roles']])
        ->assertJsonMissingPath('user.password');

    $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id, 'name' => 'tests']);
});

test('login rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

test('login validates required fields', function () {
    $this->postJson('/api/login', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

test('protected routes reject requests without a token', function () {
    $this->getJson('/api/user')->assertUnauthorized();
    $this->postJson('/api/logout')->assertUnauthorized();
});

test('a token issued by login authenticates api requests', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    $token = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret-pass'])
        ->json('token');

    $this->withToken($token)->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('id', $user->id);
});

test('logout revokes the current token', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    $token = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret-pass'])
        ->json('token');

    $this->withToken($token)->postJson('/api/logout')->assertNoContent();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
