<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'projects.read']);
    Permission::create(['name' => 'projects.create']);
    Permission::create(['name' => 'projects.update']);
    Permission::create(['name' => 'projects.delete']);

    // Create role and assign permissions
    $role = Role::create(['name' => 'user']);
    $role->givePermissionTo(['projects.read', 'projects.create', 'projects.update', 'projects.delete']);
});

test('user can list projects', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    Project::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/projects');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
            'meta' => [
                'current_page',
                'per_page',
                'total',
            ],
        ]);
});

test('user can create a project', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
            'message',
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'user_id' => $user->id,
    ]);
});

test('user can update their own project', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}", [
            'name' => 'Updated Project Name',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'name' => 'Updated Project Name',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Project Name',
    ]);
});

test('user can delete their own project', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $project = Project::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('user without permission cannot create project', function () {
    $user = User::factory()->create();
    // User has no permissions

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects', [
            'name' => 'Test Project',
        ]);

    $response->assertStatus(403);
});

