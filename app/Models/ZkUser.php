<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZkUser extends Model
{
    use HasFactory;
    
    protected $fillable = ['uid', 'userid', 'name', 'role', 'cardno', 'password', 'fingerprint_status', 'user_type'];
    //
}
