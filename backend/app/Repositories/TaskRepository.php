<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for Task model operations.
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks for a specific user.
     *
     * @param int $userId
     * @return Collection<int, Task>
     */
    public function getUserTasks($userId): Collection
    {
        return Task::where('user_id', $userId)
            ->orderBy('id', 'desc')->get();
    }

    /**
     * Create a new task.
     *
     * @param array<string, mixed> $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Get a task by ID.
     *
     * @param int $id
     * @return Task|null
     */
    public function getTaskById($id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array<string, mixed> $data
     * @return Task
     */
    public function updateTask($task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool
     */
    public function deleteTask($task): bool
    {
        return $task->delete();
    }

    public function updateMultipleTasks(array $tasksData): void
    {
       $tasksArray = array_map(fn($task) => (array) $task, $tasksData);
        logger('data', $tasksArray);
        Task::massUpdate($tasksArray);
    }
}
