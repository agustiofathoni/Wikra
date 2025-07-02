<?php

namespace App\Http\Controllers;

use App\Events\ActivityLogged;
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
            $log = ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $list->board_id,
                'action' => 'create_task',
                'target_type' => Task::class,
                'target_id' => $task->id,
                'description' => 'Card "' . $task->title . '" dibuat pada list "' . $list->name . '"',
                'created_at' => now(),
            ]);
            event(new \App\Events\ActivityLogged($log->id));
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

        // Ambil task dan simpan list_id asal SEBELUM update
        $task = Task::findOrFail($validated['task_id']);
        $oldListId = $task->list_id;

        // Ambil urutan task SEBELUM update posisi
        $oldOrder = Task::where('list_id', $oldListId)
            ->orderBy('position')->pluck('title')->toArray();

        // Update list_id task (pindah list jika perlu)
        $task->list_id = $validated['list_id'];
        $task->save();

        // Update posisi semua task di list tujuan
        foreach ($validated['tasks'] as $position => $taskId) {
            Task::where('id', $taskId)->update(['position' => $position]);
        }

        // Ambil urutan task SESUDAH update posisi
        $newOrder = Task::where('list_id', $validated['list_id'])
            ->orderBy('position')->pluck('title')->toArray();

        // Ambil nama list asal dan tujuan
        $oldList = BoardList::find($oldListId);
        $newList = BoardList::find($validated['list_id']);
        $oldListName = $oldList ? $oldList->name : '-';
        $newListName = $newList ? $newList->name : '-';

        // Buat deskripsi log
        if ($oldList && $oldList->id != $newList->id) {
            // Jika pindah list
            $desc = 'Card "' . $task->title . '" dipindah dari list "' . $oldListName . '" ke "' . $newListName . '".';
        } else {
            // Jika hanya urutan
            $desc = 'Urutan card diubah pada list "' . $newListName . '". Sebelumnya: [' . implode(', ', $oldOrder) . '] Menjadi: [' . implode(', ', $newOrder) . ']';
        }

        // Simpan log aktivitas
        $boardId = $newList ? $newList->board_id : null;
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $boardId,
            'action' => 'reorder_task',
            'target_type' => \App\Models\BoardList::class,
            'target_id' => $validated['list_id'],
            'description' => $desc,
            'created_at' => now(),
        ]);
        event(new ActivityLogged($log->id));

        // Broadcast event
        $tasks = Task::where('list_id', $validated['list_id'])
            ->orderBy('position')
            ->get(['id', 'title', 'description', 'list_id'])
            ->toArray();
        broadcast(new TaskReordered($validated['list_id'], $tasks, $boardId))->toOthers();

        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
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

             $log = ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $task->list->board_id,
                'action' => 'update_task',
                'target_type' => Task::class,
                'target_id' => $task->id,
                'description' => 'Card diubah dari "' . $oldTitle . '" menjadi "' . $task->title . '" pada list "' . $task->list->name . '"',
                'created_at' => now(),
            ]);
            event(new ActivityLogged($log->id));
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
            $log = ActivityLog::create([
                'user_id' => Auth::id(),
                'board_id' => $boardId,
                'action' => 'delete_task',
                'target_type' => Task::class,
                'target_id' => $taskId,
                'description' => 'Card "' . $taskTitle . '" dihapus dari list "' . $listName . '"',
                'created_at' => now(),
            ]);
            $task->delete();
            event(new ActivityLogged($log->id));
            // Broadcast event
            broadcast(new TaskDeleted($taskId, $listId, $boardId))->toOthers();

            return redirect()->back()->with('success', 'Card deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete card');
        }
    }


}
