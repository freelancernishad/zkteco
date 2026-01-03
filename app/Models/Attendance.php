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

    /**
     * Get human-readable verification method (state field).
     */
    public function getVerificationMethodAttribute()
    {
        return match ((int)$this->state) {
            1 => 'Fingerprint',
            4 => 'Card',
            2 => 'Card',
            0 => 'Password',
            15 => 'Face ID',
            default => 'Other (' . $this->state . ')',
        };
    }

    /**
     * Get human-readable attendance mode (type field).
     */
    public function getAttendanceModeAttribute()
    {
        return match ((int)$this->type) {
            0 => 'Check In',
            1 => 'Check Out',
            2 => 'Break Out',
            3 => 'Break In',
            4 => 'Overtime In',
            5 => 'Overtime Out',
            default => 'Other (' . $this->type . ')',
        };
    }
}
