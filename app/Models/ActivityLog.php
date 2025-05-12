<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'target_type', 'target_id', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
