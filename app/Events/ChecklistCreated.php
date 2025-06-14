<?php
namespace App\Events;

use App\Models\Checklist;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChecklistCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $checklist;

    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;
    }

    public function broadcastOn()
    {
        return new Channel('task.' . $this->checklist->task_id);
    }

    public function broadcastWith()
    {
        return [
            'checklist' => $this->checklist->toArray()
        ];
    }
}
