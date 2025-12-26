<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['uid', 'user_id', 'state', 'timestamp', 'type'];

    public function user()
    {
        return $this->belongsTo(ZkUser::class, 'user_id', 'userid');
    }
}
