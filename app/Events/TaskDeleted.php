<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $taskId;
    public $listId;
    public $boardId;

    public function __construct($taskId, $listId, $boardId)
    {
        $this->taskId = $taskId;
        $this->listId = $listId;
        $this->boardId = $boardId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('board.' . $this->boardId)
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->taskId,
            'list_id' => $this->listId,
        ];
    }
}
