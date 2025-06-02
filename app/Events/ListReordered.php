<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $boardId;
    public $lists; // array of list data

    public function __construct($boardId, $lists)
    {
        $this->boardId = $boardId;
        $this->lists = $lists;
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
            'lists' => $this->lists,
        ];
    }
}
