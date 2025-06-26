<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskReordered;
use App\Events\TaskUpdated;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\BoardList;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'list_id' => 'required|exists:lists,id'
        ]);

        DB::beginTransaction();
        try {
            $list = BoardList::findOrFail($validated['list_id']);
            $maxPosition = $list->tasks()->max('position') ?? 0;
            $task = new Task();
            $task->title = $validated['title'];
            $task->description = $validated['description'];
            $task->list_id = $validated['list_id'];
            $task->position = $maxPosition + 1;
            $task->save();

              // Activity log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $list->board_id,
                'action' => 'create_task',
                'target_type' => Task::class,
                'target_id' => $task->id,
                'description' => 'Card "' . $task->title . '" dibuat pada list "' . $list->name . '"',
                'created_at' => now(),
            ]);
            broadcast(new TaskCreated($task))->toOthers();
            DB::commit();

        return response()->json(['success' => true, 'task' => $task]);
         } catch (\Exception $e) {
             DB::rollback();
             return back()->with('error', 'Failed to add card');
         }
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'list_id' => 'required|exists:lists,id',
            'tasks' => 'required|array',
            'tasks.*' => 'numeric|exists:tasks,id'
        ]);

        try {
            DB::beginTransaction();

            $task = Task::findOrFail($validated['task_id']);
            $task->list_id = $validated['list_id'];
            $task->save();

            foreach ($validated['tasks'] as $position => $taskId) {
                Task::where('id', $taskId)->update(['position' => $position]);
            }

            // Ambil data task terbaru untuk list ini
            $tasks = Task::where('list_id', $validated['list_id'])
                ->orderBy('position')
                ->get(['id', 'title', 'description', 'list_id'])
                ->toArray();

            // Broadcast event
            $boardId = $task->list->board_id;
             // Activity log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $boardId,
                'action' => 'reorder_task',
                'target_type' => \App\Models\BoardList::class,
                'target_id' => $validated['list_id'],
                'description' => 'Urutan card diubah pada list "' . $task->list->name . '"',
                'created_at' => now(),
            ]);
            broadcast(new TaskReordered($validated['list_id'], $tasks, $boardId))->toOthers();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false], 500);
        }
    }

   public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $oldTitle = $task->title;
            $task->update($validated);

             ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $task->list->board_id,
                'action' => 'update_task',
                'target_type' => Task::class,
                'target_id' => $task->id,
                'description' => 'Card diubah dari "' . $oldTitle . '" menjadi "' . $task->title . '" pada list "' . $task->list->name . '"',
                'created_at' => now(),
            ]);

            // Broadcast event
            broadcast(new TaskUpdated($task))->toOthers();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $listId = $task->list_id;
            $boardId = $task->list->board_id;
            $taskId = $task->id;
            $taskTitle = $task->title;
            $listName = $task->list->name;
             ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $boardId,
                'action' => 'delete_task',
                'target_type' => Task::class,
                'target_id' => $taskId,
                'description' => 'Card "' . $taskTitle . '" dihapus dari list "' . $listName . '"',
                'created_at' => now(),
            ]);
            $task->delete();

            // Broadcast event
            broadcast(new TaskDeleted($taskId, $listId, $boardId))->toOthers();

            return redirect()->back()->with('success', 'Card deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete card');
        }
    }


}
