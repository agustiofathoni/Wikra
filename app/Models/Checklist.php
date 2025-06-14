<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = ['task_id', 'item_text', 'is_completed'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
