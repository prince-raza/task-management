<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

// uses(RefreshDatabase::class);

test('user can login with valid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/logout');

    $response->assertStatus(200);
});

test('unauthenticated user cannot logout', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});

test('logout deletes all user tokens', function () {
    $user = User::factory()->create();
    $token1 = $user->createToken('token-1')->plainTextToken;
    $user->createToken('token-2')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
        ->postJson('/api/logout');

    $response->assertStatus(200);
});
