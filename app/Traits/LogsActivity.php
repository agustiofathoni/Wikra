<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected function logActivity(string $action, string $description, $target = null)
    {
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'board_id' => $this->getBoardId(),
            'action' => $action,
            'description' => $description,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target ? $target->id : null,
            'created_at' => now(),
        ]);

        event(new \App\Events\ActivityLogged($log->id));
    }

    /**
     * Get the board ID for the activity log.
     *
     * This method should be implemented by the class using this trait.
     */
    abstract protected function getBoardId(): int;
}
