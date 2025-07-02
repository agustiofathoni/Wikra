<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Task;
use App\Services\ChecklistService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChecklistController extends Controller
{
    protected $checklistService;

    public function __construct(ChecklistService $checklistService)
    {
        $this->checklistService = $checklistService;
    }

    public function index(Task $task)
    {
        return response()->json($task->checklists()->get());
    }

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'item_text' => 'required|string|max:255',
        ]);

        $checklist = $this->checklistService->createChecklist($task, $request->only('item_text'));

        return response()->json($checklist);
    }

    public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'is_completed' => 'nullable|boolean',
            'item_text' => 'nullable|string|max:255',
        ]);

        $checklist = $this->checklistService->updateChecklist($checklist, $request->only(['is_completed', 'item_text']));

        return response()->json($checklist);
    }

    public function destroy(Checklist $checklist)
    {
        $this->checklistService->deleteChecklist($checklist);

        return response()->json(['success' => true]);
    }
}
