<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChecklistDeleted implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $checklistId;
    public $taskId;

    public function __construct($checklistId, $taskId)
    {
        $this->checklistId = $checklistId;
        $this->taskId = $taskId;
    }

    public function broadcastOn()
    {
        return new Channel('task.' . $this->taskId);
    }

    public function broadcastWith()
    {
        return [
            'checklist_id' => $this->checklistId
        ];
    }
}
