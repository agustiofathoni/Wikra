<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
     protected $fillable = ['title', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lists()
    {
        return $this->hasMany(BoardList::class)->orderBy('position');
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'collaborators');
    }
}
