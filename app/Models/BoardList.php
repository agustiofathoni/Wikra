<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardList extends Model
{
    protected $table = 'lists';
    protected $fillable = ['name', 'board_id', 'position'];

     protected static function boot()
    {
        parent::boot();
        static::creating(function ($list) {
            if (!$list->position) {
                $list->position = static::where('board_id', $list->board_id)->max('position') + 1;
            }
        });
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id');
    }
}
