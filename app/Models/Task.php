<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'list_id', 'position'];
    
    public function list()
    {
        return $this->belongsTo(BoardList::class, 'list_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'task_labels');
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }
}
