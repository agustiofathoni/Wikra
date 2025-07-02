<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;


    protected $fillable = ['user_id', 'board_id', 'action', 'target_type', 'target_id', 'description', 'created_at'];

     protected $casts = [
        'created_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function target()
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }
}
