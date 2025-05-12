<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_labels');
    }
}
