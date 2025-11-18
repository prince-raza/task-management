<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use function Laravel\Prompts\info;

class TaskController extends Controller
{
    use AuthorizesRequests;
    protected $tasks;

    public function __construct(TaskRepositoryInterface $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Return all tasks.
     *
     * @return TaskCollection
     */
    public function index()
    {
        $tasks = $this->tasks->getUserTasks(Auth::user()->id);

        return new TaskCollection($tasks);
    }

    /**
     * Store a new task in the database.
     *
     * @param  StoreTaskRequest  $request
     * @return TaskResource
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = array_merge(
            $request->validated(),
            ['user_id' => Auth::id()]
        );

        $task = $this->tasks->createTask($validated);

        return new TaskResource($task);
    }

    /**
     * Return a single task by ID.
     *
     * @param Task $task
     * @return TaskResource
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task = $this->tasks->getTaskById($task->id);

        return new TaskResource($task);
    }

    /**
     * Update an existing task in the database.
     *
     * @param  UpdateTaskRequest  $request
     * @param  Task  $task
     * @return TaskResource
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $updatedTask = $this->tasks->updateTask($task, $request->validated());

        return new TaskResource($updatedTask);
    }

    /**
     * Delete a task by ID.
     *
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Task::find($id));

        $task = $this->tasks->getTaskById($id);

        $this->tasks->deleteTask($task);

        return response()->noContent();
    }

    /**
     * Bulk update tasks in the database.
     *
     * @param Request $request
     * @return Response
     */
    public function bulkUpdate(Request $request)
    {
        $tasksData = $request->input('ordered');

        foreach ($tasksData as $taskData) {
            $task = $this->tasks->getTaskById($taskData['id']);
            $this->authorize('update', $task);
        }

        $this->tasks->updateMultipleTasks($tasksData);

        return response()->noContent();
    }
}
