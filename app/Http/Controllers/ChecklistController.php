<?php

namespace App\Http\Controllers;

use App\Events\ChecklistCreated;
use App\Events\ChecklistDeleted;
use App\Events\ChecklistUpdated;
use App\Models\ActivityLog;
use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    public function index(Task $task)
    {
        return response()->json($task->checklists()->get());
    }

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'item_text' => 'required|string|max:255',
        ]);
        $checklist = $task->checklists()->create([
            'item_text' => $request->item_text,
            'is_completed' => false,
        ]);
        // Activity log
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $task->list->board_id,
            'action' => 'create_checklist',
            'target_type' => Checklist::class,
            'target_id' => $checklist->id,
            'description' => 'Checklist "' . $checklist->item_text . '" dibuat pada card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '"',
            'created_at' => now(),
        ]);
        event(new \App\Events\ActivityLogged($log->id));
        broadcast(new ChecklistCreated($checklist))->toOthers();
        return response()->json($checklist);
    }

   public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'is_completed' => 'nullable|boolean',
            'item_text' => 'nullable|string|max:255',
        ]);

        $data = [];
        if ($request->has('is_completed')) {
            $data['is_completed'] = $request->is_completed;
        }
        if ($request->has('item_text')) {
            $oldText = $checklist->item_text;
            $data['item_text'] = $request->item_text;
        }

        $checklist->update($data);
         // Activity log
        $task = $checklist->task;
        $desc = '';
        if ($request->has('item_text')) {
            $desc = 'Checklist diubah dari "' . $oldText . '" menjadi "' . $checklist->item_text . '" pada card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '"';
        } elseif ($request->has('is_completed')) {
            $desc = 'Checklist "' . $checklist->item_text . '" pada card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '" ' . ($checklist->is_completed ? 'diselesaikan' : 'dibuka kembali');
        }

        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $task->list->board_id,
            'action' => 'update_checklist',
            'target_type' => Checklist::class,
            'target_id' => $checklist->id,
            'description' => $desc,
            'created_at' => now(),
        ]);
        event(new \App\Events\ActivityLogged($log->id));
        broadcast(new ChecklistUpdated($checklist))->toOthers();

        return response()->json($checklist);
    }

    public function destroy(Checklist $checklist)
    {
        $id = $checklist->id;
        $taskId = $checklist->task_id;
        $task = $checklist->task;
        $itemText = $checklist->item_text;
        // Activity log
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $task->list->board_id,
            'action' => 'delete_checklist',
            'target_type' => Checklist::class,
            'target_id' => $id,
            'description' => 'Checklist "' . $itemText . '" dihapus dari card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '"',
            'created_at' => now(),
        ]);
        $checklist->delete();
        event(new \App\Events\ActivityLogged($log->id));
        broadcast(new ChecklistDeleted($id, $taskId))->toOthers();
        return response()->json(['success' => true]);
    }
}
