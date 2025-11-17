<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view the task.
     *
     * @param  User $user
     * @param  Task $task
     * @return Response|bool
     */
    public function view(User $user, Task $task)
    {
        return $task->user_id === $user->id;
    }


    /**
     * Determine whether the user can update the task.
     *
     * @param  User $user
     * @param  Task $task
     * @return Response|bool
     */
    public function update(User $user, Task $task)
    {
        return $task->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param  User $user
     * @param  Task $task
     * @return Response|bool
     *
     * Deletes a task if the user is the owner of the task.
     */
    public function delete(User $user, Task $task)
    {
        return $task->user_id === $user->id;
    }
}
