<?php

namespace App\Events;

use App\Models\BoardList;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $list;

    public function __construct(BoardList $list)
    {
        $this->list = $list;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('board.' . $this->list->board_id)
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'list' => [
                'id' => $this->list->id,
                'name' => $this->list->name,
                'position' => $this->list->position,
                'board_id' => $this->list->board_id,
            ]
        ];
    }
}
