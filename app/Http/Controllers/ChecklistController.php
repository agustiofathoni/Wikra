<?php

namespace App\Http\Controllers;

use App\Events\ChecklistCreated;
use App\Events\ChecklistDeleted;
use App\Events\ChecklistUpdated;
use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Http\Request;

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
            $data['item_text'] = $request->item_text;
        }

        $checklist->update($data);
        broadcast(new ChecklistUpdated($checklist))->toOthers();

        return response()->json($checklist);
    }

    public function destroy(Checklist $checklist)
    {
        $id = $checklist->id;
        $taskId = $checklist->task_id;
        $checklist->delete();
        broadcast(new ChecklistDeleted($id, $taskId))->toOthers();
        return response()->json(['success' => true]);
    }
}
