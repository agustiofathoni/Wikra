<?php
namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ActivityLogged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    protected $activityId;

    public function __construct($activityId)
    {
        $this->activityId = $activityId;
    }

    public function broadcastOn()
    {
        $activity = ActivityLog::with('user')->find($this->activityId);
        return new Channel('board.' . ($activity ? $activity->board_id : 'unknown'));
    }

    public function broadcastWith()
    {
        $activity = ActivityLog::with('user')->find($this->activityId);
        return [
            'activity' => $activity ? [
                'id' => $activity->id,
                'user' => $activity->user ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                ] : null,
                'action' => $activity->action,
                'target_type' => $activity->target_type,
                'target_id' => $activity->target_id,
                'description' => $activity->description,
                // Paksa casting ke Carbon, apapun tipenya!
                'created_at' => $activity->created_at
                    ? \Carbon\Carbon::parse($activity->created_at)->timezone('Asia/Jakarta')->toISOString()
                    : null,
            ] : null
        ];
    }
}
