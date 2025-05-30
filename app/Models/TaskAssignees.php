<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignees extends Model
{
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
