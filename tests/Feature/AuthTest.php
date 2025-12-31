<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
            'message',
        ]);
});

test('user cannot login with invalid credentials', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid credentials',
        ]);
});

test('authenticated user can get their profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'roles',
                'permissions',
            ],
        ]);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);
});

