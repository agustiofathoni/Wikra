<?php

namespace App\Services;

use App\Events\ChecklistCreated;
use App\Events\ChecklistDeleted;
use App\Events\ChecklistUpdated;
use App\Models\Checklist;
use App\Models\Task;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChecklistService
{
    use LogsActivity;

    private $boardId;

    protected function getBoardId(): int
    {
        return $this->boardId;
    }

    public function createChecklist(Task $task, array $data): Checklist
    {
        $this->boardId = $task->list->board_id;

        return DB::transaction(function () use ($task, $data) {
            $checklist = $task->checklists()->create([
                'item_text' => $data['item_text'],
                'is_completed' => false,
            ]);

            $this->logActivity('create_checklist', 'Checklist "' . $checklist->item_text . '" dibuat pada card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '"', $checklist);

            broadcast(new ChecklistCreated($checklist))->toOthers();

            return $checklist;
        });
    }

    public function updateChecklist(Checklist $checklist, array $data): Checklist
    {
        $this->boardId = $checklist->task->list->board_id;
        $task = $checklist->task;
        $oldText = $checklist->item_text;

        $checklist->update($data);

        $desc = '';
        if (isset($data['item_text'])) {
            $desc = 'Checklist diubah dari "' . $oldText . '" menjadi "' . $checklist->item_text . '" pada card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '"';
        } elseif (isset($data['is_completed'])) {
            $desc = 'Checklist "' . $checklist->item_text . '" pada card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '" ' . ($checklist->is_completed ? 'diselesaikan' : 'dibuka kembali');
        }

        $this->logActivity('update_checklist', $desc, $checklist);

        broadcast(new ChecklistUpdated($checklist))->toOthers();

        return $checklist;
    }

    public function deleteChecklist(Checklist $checklist)
    {
        $this->boardId = $checklist->task->list->board_id;
        $id = $checklist->id;
        $taskId = $checklist->task_id;
        $task = $checklist->task;
        $itemText = $checklist->item_text;

        $this->logActivity('delete_checklist', 'Checklist "' . $itemText . '" dihapus dari card "' . $task->title . '" list "' . ($task->list->name ?? '-') . '"', $checklist);

        $checklist->delete();

        broadcast(new ChecklistDeleted($id, $taskId))->toOthers();
    }
}
