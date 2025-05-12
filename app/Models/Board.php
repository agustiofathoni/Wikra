<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lists()
    {
        return $this->hasMany(BoardList::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'collaborators');
    }
}
