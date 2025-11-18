<?php

declare(strict_types=1);

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->token = $this->user->createToken('test-token')->plainTextToken;
});

test('unauthenticated user cannot access tasks', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertStatus(401);
});

test('authenticated user can get their tasks', function () {
    Task::factory()->count(3)->create(['user_id' => $this->user->id]);
    Task::factory()->count(2)->create(['user_id' => $this->otherUser->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'description',
                    'date',
                    'status',
                    'priority',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(3);
});

test('authenticated user can create a task', function () {
    $taskData = [
        'user_id' => $this->user->id,
        'description' => 'Test task description',
        'date' => now()->format('Y-m-d'),
        'status' => TaskStatus::PENDING,
        'priority' => TaskPriority::MEDIUM,
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/tasks', $taskData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'description',
                'date',
                'status',
                'priority',
                'created_at',
                'updated_at',
            ],
        ]);

    expect($response->json('data.description'))->toBe('Test task description');
    expect($response->json('data.status'))->toBe(TaskStatus::PENDING);
    expect($response->json('data.priority'))->toBe(TaskPriority::MEDIUM);

    $this->assertDatabaseHas('tasks', [
        'user_id' => $this->user->id,
        'description' => 'Test task description',
    ]);
});

test('authenticated user can view their own task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'description',
                'date',
                'status',
                'priority',
                'created_at',
                'updated_at',
            ],
        ]);

    expect($response->json('data.id'))->toBe($task->id);
});

test('authenticated user cannot view other user task', function () {
    $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('authenticated user can update their own task', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'status' => TaskStatus::PENDING,
    ]);

    $updateData = [
        'description' => 'Updated description',
        'status' => TaskStatus::IN_PROGRESS,
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/tasks/{$task->id}", $updateData);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'description',
                'date',
                'status',
                'priority',
                'created_at',
                'updated_at',
            ],
        ]);

    expect($response->json('data.description'))->toBe('Updated description');
    expect($response->json('data.status'))->toBe(TaskStatus::IN_PROGRESS);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'description' => 'Updated description',
        'status' => TaskStatus::IN_PROGRESS,
    ]);
});

test('authenticated user cannot update other user task', function () {
    $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

    $updateData = [
        'description' => 'Updated description',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/tasks/{$task->id}", $updateData);

    $response->assertStatus(403);
});

test('authenticated user can delete their own task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('authenticated user cannot delete other user task', function () {
    $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});

test('all endpoints fail with invalid token', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    // GET /api/tasks
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->getJson('/api/tasks');
    $response->assertStatus(401);

    // POST /api/tasks
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->postJson('/api/tasks', []);
    $response->assertStatus(401);

    // GET /api/tasks/{id}
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->getJson("/api/tasks/{$task->id}");
    $response->assertStatus(401);

    // PUT /api/tasks/{id}
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->putJson("/api/tasks/{$task->id}", []);
    $response->assertStatus(401);

    // DELETE /api/tasks/{id}
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->deleteJson("/api/tasks/{$task->id}");
    $response->assertStatus(401);
});