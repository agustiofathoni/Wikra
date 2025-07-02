<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask($request->validated());

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function reorder(ReorderTaskRequest $request)
    {
        $this->taskService->reorderTasks($request->validated());

        return response()->json(['success' => true]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->updateTask($task, $request->validated());

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        $this->authorize('update', $task->list->board);

        $this->taskService->deleteTask($task);

        return redirect()->back()->with('success', 'Card deleted successfully');
    }
}

