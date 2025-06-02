<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $listId;
    public $boardId;

    public function __construct($listId, $boardId)
    {
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
            'list_id' => $this->listId,
        ];
    }
}
