<?php

namespace App\Services;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskReordered;
use App\Events\TaskUpdated;
use App\Models\BoardList;
use App\Models\Task;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;

class TaskService
{
    use LogsActivity;

    private $board;

    protected function getBoardId(): int
    {
        return $this->board->id;
    }

    public function createTask(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $list = BoardList::findOrFail($data['list_id']);
            $this->board = $list->board;
            $maxPosition = $list->tasks()->max('position') ?? 0;

            $task = new Task();
            $task->title = $data['title'];
            $task->description = $data['description'];
            $task->list_id = $data['list_id'];
            $task->position = $maxPosition + 1;
            $task->save();

            broadcast(new TaskCreated($task))->toOthers();

            $this->logActivity('create_task', 'Card "' . $task->title . '" dibuat pada list "' . $list->name . '"', $task);

            return $task;
        });
    }

    public function updateTask(Task $task, array $data): Task
    {
        $this->board = $task->list->board;
        $oldTitle = $task->title;

        $task->update($data);

        broadcast(new TaskUpdated($task))->toOthers();

        $this->logActivity('update_task', 'Card diubah dari "' . $oldTitle . '" menjadi "' . $task->title . '" pada list "' . $task->list->name . '"', $task);

        return $task;
    }

    public function reorderTasks(array $data)
    {
        DB::transaction(function () use ($data) {
            $task = Task::findOrFail($data['task_id']);
            $oldList = $task->list;
            $newList = BoardList::findOrFail($data['list_id']);
            $this->board = $newList->board;

            $task->list_id = $newList->id;
            $task->save();

            foreach ($data['tasks'] as $position => $taskId) {
                Task::where('id', $taskId)->update(['position' => $position]);
            }

            $tasks = Task::where('list_id', $newList->id)
                ->orderBy('position')
                ->get(['id', 'title', 'description', 'list_id'])
                ->toArray();

            broadcast(new TaskReordered($newList->id, $tasks, $this->board->id))->toOthers();

            if ($oldList->id !== $newList->id) {
                $this->logActivity('reorder_task', 'Card "' . $task->title . '" dipindah dari list "' . $oldList->name . '" ke "' . $newList->name . '"', $task);
            } else {
                $this->logActivity('reorder_task', 'Urutan card diubah pada list "' . $newList->name . '"', $task);
            }
        });
    }

    public function deleteTask(Task $task)
    {
        $this->board = $task->list->board;
        $listId = $task->list_id;
        $boardId = $this->board->id;
        $taskId = $task->id;
        $taskTitle = $task->title;
        $listName = $task->list->name;

        broadcast(new TaskDeleted($taskId, $listId, $boardId))->toOthers();

        $this->logActivity('delete_task', 'Card "' . $taskTitle . '" dihapus dari list "' . $listName . '"', $task);

        $task->delete();
    }
}
