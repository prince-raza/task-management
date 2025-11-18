<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Task repository operations.
 */
interface TaskRepositoryInterface
{
    public function getUserTasks($userId);
    public function createTask(array $data);
    public function getTaskById($id);
    public function updateTask($task, array $data);
    public function deleteTask($task);
    public function updateMultipleTasks(array $tasksData): void;
}

